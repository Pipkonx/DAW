<?php

namespace App\Services\Financial\Import;

use Smalot\PdfParser\Parser;
use Carbon\Carbon;

class PdfImportEngine
{
    /**
     * Extrae transacciones de un archivo PDF.
     */
    public function parse($file)
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
                
                // Buscar patrones de fecha
                if (preg_match('/(\d{4}-\d{2}-\d{2}|\d{2}\/\d{2}\/\d{4})/', $line, $dateMatch)) {
                    $dateStr = $dateMatch[0];
                    $rest = str_replace($dateStr, '', $line);
                    
                    $type = 'buy';
                    if (stripos($rest, 'sell') !== false || stripos($rest, 'venta') !== false) $type = 'sell';
                    elseif (stripos($rest, 'dividend') !== false || stripos($rest, 'dividendo') !== false) $type = 'dividend';
                    
                    preg_match_all('/[\d\.,]+/', $rest, $numberMatches);
                    $numbers = array_map([ImportHelper::class, 'parseNumber'], $numberMatches[0] ?? []);
                    $numbers = array_filter($numbers, fn($n) => $n > 0);
                    
                    $ticker = $this->extractTickerFromText($rest);
                    
                    if (count($numbers) >= 2) {
                        $quantity = $numbers[0];
                        $price = $numbers[1];
                        $transactions[] = [
                            'date' => Carbon::parse($dateStr)->format('Y-m-d'),
                            'ticker' => $ticker,
                            'name' => $ticker,
                            'type' => $type,
                            'quantity' => $quantity,
                            'price_per_unit' => $price,
                            'amount' => count($numbers) >= 3 ? $numbers[2] : ($quantity * $price),
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            // Log error if needed
        }
        return $transactions;
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
}
