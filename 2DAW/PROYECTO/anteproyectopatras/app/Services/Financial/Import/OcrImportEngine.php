<?php

namespace App\Services\Financial\Import;

use Illuminate\Support\Facades\Http;

class OcrImportEngine
{
    /**
     * Extrae transacciones de una imagen usando el servicio OCR.space.
     */
    public function parse($file)
    {
        $transactions = [];
        try {
            $imageContent = file_get_contents($file->getRealPath());
            $response = Http::asMultipart()
                ->attach('file', $imageContent, $file->getClientOriginalName())
                ->post('https://api.ocr.space/parse/image', [
                    'apikey' => 'helloworld', // Demo key
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
        } catch (\Exception $e) {
            // Log error
        }
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

            if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{2,4})$/', $line, $dateMatch)) {
                $currentDate = ImportHelper::parseDate($line) ?? $currentDate;
                continue;
            }

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

    private function extractAmountFromLine($line)
    {
        preg_match('/([-+]?[\d\.,]+)/', $line, $match);
        return ImportHelper::parseNumber($match[1] ?? 0);
    }

    private function finalizeOcrTx($tx)
    {
        if ($tx['quantity'] > 0 && $tx['amount'] > 0) $tx['price_per_unit'] = $tx['amount'] / $tx['quantity'];
        $tx['ticker'] = $tx['ticker'] ?? $tx['name'];
        return $tx;
    }
}
