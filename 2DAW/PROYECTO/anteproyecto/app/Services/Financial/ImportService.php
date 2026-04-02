<?php

namespace App\Services\Financial;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Smalot\PdfParser\Parser;
use Carbon\Carbon;
use App\Services\Asset\AssetService;

class ImportService
{
    protected $assetService;

    public function __construct(AssetService $assetService)
    {
        $this->assetService = $assetService;
    }

    /**
     * Parse a file (CSV, PDF, or Image) and return a list of preview transactions.
     */
    public function previewFromFile($file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (in_array($extension, ['csv', 'txt'])) {
            return $this->parseCsv($file);
        } elseif ($extension === 'pdf') {
            return $this->parsePdf($file);
        } elseif (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return $this->parseImageViaOcr($file);
        }
        
        return [];
    }

    /**
     * Finalize and link a preview transaction.
     */
    public function finalizeTransaction(array $tx, $userId)
    {
        $name = trim($tx['name'] ?? 'Activo Desconocido');
        if (empty($name)) $name = "Activo Desconocido";

        // Calculate price if missing
        if (($tx['quantity'] ?? 0) > 0 && ($tx['amount'] ?? 0) > 0 && ($tx['price_per_unit'] ?? 0) == 0) {
            $tx['price_per_unit'] = $tx['amount'] / $tx['quantity'];
        }

        $typeHint = $this->guessAssetType($name);
        
        // Use AssetService to find or link
        $asset = $this->assetService->findOrCreateAndLink($userId, [
            'name' => $name,
            'ticker' => $tx['ticker'] ?? $name,
            'type' => $typeHint,
            'price_per_unit' => $tx['price_per_unit'] ?? 0
        ]);

        return array_merge($tx, [
            'asset_id' => $asset->id,
            'ticker' => $asset->ticker,
            'isin' => $asset->isin,
            'asset_type' => $asset->type,
            'name' => $asset->name,
            'link_status' => $asset->market_asset_id ? 'linked' : 'pending',
            'original_name' => $name,
            'nav_date' => $tx['date']
        ]);
    }

    private function parseCsv($file)
    {
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));
        $header = array_shift($data);
        $transactions = [];

        $map = $this->getHeuristicMapping($header);

        foreach ($data as $row) {
            if (count($row) < 3) continue;

            $tx = [
                'date' => $this->parseDate($row[$map['date']] ?? null),
                'ticker' => $map['ticker'] > -1 ? ($row[$map['ticker']] ?? 'UNKNOWN') : 'UNKNOWN',
                'type' => $this->normalizeType($map['type'] > -1 ? ($row[$map['type']] ?? 'buy') : 'buy'),
                'quantity' => $this->parseNumber($row[$map['quantity']] ?? 0),
                'price_per_unit' => $this->parseNumber($row[$map['price']] ?? 0),
                'amount' => $this->parseNumber($row[$map['amount']] ?? 0),
            ];

            $transactions[] = $tx;
        }

        return $transactions;
    }

    private function parsePdf($file)
    {
        $transactions = [];
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($file->getRealPath());
            $text = $pdf->getText();
            $lines = explode("\n", $text);
            
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                if (preg_match('/(\d{4}-\d{2}-\d{2}|\d{2}\/\d{2}\/\d{4})/', $line, $dateMatch)) {
                    $dateStr = $dateMatch[0];
                    $rest = str_replace($dateStr, '', $line);
                    
                    $type = 'buy';
                    if (stripos($rest, 'sell') !== false || stripos($rest, 'venta') !== false) $type = 'sell';
                    elseif (stripos($rest, 'dividend') !== false || stripos($rest, 'dividendo') !== false) $type = 'dividend';
                    
                    preg_match_all('/[\d\.,]+/', $rest, $numberMatches);
                    $numbers = array_map([$this, 'parseNumber'], $numberMatches[0] ?? []);
                    $numbers = array_filter($numbers, fn($n) => $n > 0);
                    
                    $ticker = $this->extractTickerFromText($rest);
                    
                    if (count($numbers) >= 2) {
                        $quantity = $numbers[0];
                        $price = $numbers[1];
                        $transactions[] = [
                            'date' => Carbon::parse($dateStr)->format('Y-m-d'),
                            'ticker' => $ticker,
                            'type' => $type,
                            'quantity' => $quantity,
                            'price_per_unit' => $price,
                            'amount' => count($numbers) >= 3 ? $numbers[2] : ($quantity * $price),
                        ];
                    }
                }
            }
        } catch (\Exception $e) {}
        return $transactions;
    }

    private function parseImageViaOcr($file)
    {
        $transactions = [];
        try {
            $imageContent = file_get_contents($file->getRealPath());
            $response = Http::asMultipart()
                ->attach('file', $imageContent, $file->getClientOriginalName())
                ->post('https://api.ocr.space/parse/image', [
                    'apikey' => 'helloworld',
                    'language' => 'eng',
                    'OCREngine' => '2',
                ]);

            if ($response->successful()) {
                $result = $response->json();
                if (isset($result['ParsedResults'][0]['ParsedText'])) {
                    $text = $result['ParsedResults'][0]['ParsedText'];
                    $transactions = $this->parseOcrText($text);
                }
            }
        } catch (\Exception $e) {}
        return $transactions;
    }

    private function parseOcrText($text)
    {
        $transactions = [];
        $lines = explode("\n", $text);
        $currentDate = now()->format('Y-m-d');
        $pendingTx = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Date context update
            if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{2,4})$/', $line, $dateMatch)) {
                $currentDate = $this->parseDate($line) ?? $currentDate;
                continue;
            }

            // Transaction Start Heuristic
            $isStart = false;
            $type = 'buy';
            if (stripos($line, 'Suscripción') !== false || stripos($line, 'Compra') !== false) {
                $isStart = true;
            } elseif (stripos($line, 'Reembolso') !== false || stripos($line, 'Venta') !== false) {
                $isStart = true;
                $type = 'sell';
            }

            if ($isStart) {
                if ($pendingTx) $transactions[] = $this->finalizeOcrTx($pendingTx);
                
                $pendingTx = [
                    'date' => $currentDate,
                    'type' => $type,
                    'amount' => $this->extractAmountFromLine($line),
                    'quantity' => 0,
                    'price_per_unit' => 0,
                    'name' => '',
                    'original_text' => $line,
                    'state' => 'WAITING_NAME'
                ];
                continue;
            }

            // Continue building pending TX (Name and Quantity)
            if ($pendingTx) {
                if ($pendingTx['state'] === 'WAITING_NAME' && strlen($line) > 2) {
                    $pendingTx['name'] = $line;
                    $pendingTx['state'] = 'WAITING_QUANTITY';
                } elseif (stripos($line, 'participaciones') !== false || stripos($line, 'títulos') !== false) {
                    $pendingTx['quantity'] = $this->extractAmountFromLine($line);
                    $transactions[] = $this->finalizeOcrTx($pendingTx);
                    $pendingTx = null;
                }
            }
        }
        
        if ($pendingTx) $transactions[] = $this->finalizeOcrTx($pendingTx);
        return $transactions;
    }

    // Helper functions moved from controllers...
    public function parseNumber($str)
    {
        $str = preg_replace('/[^\d,.-]/', '', trim($str));
        $lastComma = strrpos($str, ',');
        $lastDot = strrpos($str, '.');
        
        if ($lastComma !== false && ($lastDot === false || $lastComma > $lastDot)) {
            $str = str_replace('.', '', $str);
            $str = str_replace(',', '.', $str);
        } else {
            $str = str_replace(',', '', $str);
        }
        return (float) $str;
    }

    private function getHeuristicMapping($header)
    {
        $map = ['date' => -1, 'ticker' => -1, 'type' => -1, 'quantity' => -1, 'price' => -1, 'amount' => -1];
        foreach ($header as $index => $col) {
            $col = strtolower(trim($col));
            if (str_contains($col, 'date') || str_contains($col, 'fecha')) $map['date'] = $index;
            if (str_contains($col, 'ticker') || str_contains($col, 'symbol') || str_contains($col, 'activo') || str_contains($col, 'isin') || str_contains($col, 'name')) $map['ticker'] = $index;
            if (str_contains($col, 'type') || str_contains($col, 'tipo')) $map['type'] = $index;
            if (str_contains($col, 'quantity') || str_contains($col, 'cantidad')) $map['quantity'] = $index;
            if (str_contains($col, 'price') || str_contains($col, 'precio')) $map['price'] = $index;
            if (str_contains($col, 'amount') || str_contains($col, 'total') || str_contains($col, 'valor')) $map['amount'] = $index;
        }
        return $map;
    }

    private function normalizeType($type)
    {
        $type = strtolower($type);
        if (str_contains($type, 'compra') || str_contains($type, 'buy')) return 'buy';
        if (str_contains($type, 'venta') || str_contains($type, 'sell')) return 'sell';
        if (str_contains($type, 'dividendo') || str_contains($type, 'dividend')) return 'dividend';
        return 'buy';
    }

    private function parseDate($str)
    {
        try { return Carbon::parse($str)->format('Y-m-d'); } catch (\Exception $e) { return null; }
    }

    private function guessAssetType($name)
    {
        if (preg_match('/(Fondo|Fund|Index|Indice|Acc|Clase|Sicav)/i', $name)) return 'fund';
        if (stripos($name, 'ETF') !== false) return 'etf';
        if (preg_match('/(Bitcoin|BTC|ETH|Crypto)/i', $name)) return 'crypto';
        return 'stock';
    }

    private function extractTickerFromText($text)
    {
        if (preg_match('/\b[A-Z]{2}[A-Z0-9]{9}\d\b/', $text, $match)) return $match[0];
        $words = explode(' ', $text);
        foreach ($words as $word) {
            if (preg_match('/^[A-Z]{1,5}$/', $word) && !is_numeric($word)) return $word;
        }
        return 'UNKNOWN';
    }

    private function extractAmountFromLine($line)
    {
        preg_match('/([-+]?[\d\.,]+)/', $line, $match);
        return $this->parseNumber($match[1] ?? 0);
    }

    private function finalizeOcrTx($tx)
    {
        if ($tx['quantity'] > 0 && $tx['amount'] > 0) $tx['price_per_unit'] = $tx['amount'] / $tx['quantity'];
        $tx['ticker'] = $tx['ticker'] ?? $tx['name'];
        return $tx;
    }
}
