<?php

namespace App\Services\Financial\Import;

class CsvImportEngine
{
    /**
     * Parsea un archivo CSV y devuelve una lista de transacciones.
     */
    public function parse($file)
    {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        
        // Detectar codificación y convertir a UTF-8 si es necesario
        $encoding = mb_detect_encoding($content, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }
        
        // Separar líneas de forma robusta (soporta \r\n, \n y \r)
        $lines = preg_split('/\r\n|\r|\n/', $content);
        $lines = array_values(array_filter(array_map('trim', $lines)));
        
        if (empty($lines)) return [];

        // Detectar delimitador (heurística)
        $testLine = $lines[0];
        if (count($lines) > 1 && strlen($testLine) < 5) $testLine = $lines[1]; 
        
        $commaCount = substr_count($testLine, ',');
        $semicolonCount = substr_count($testLine, ';');
        $delimiter = ($semicolonCount > $commaCount) ? ';' : ',';

        // Convertir todas las líneas a arrays
        $data = array_map(fn($line) => str_getcsv($line, $delimiter), $lines);
        
        // Buscar la cabecera real
        $headerIndex = 0;
        foreach ($data as $index => $row) {
            $mapping = $this->getHeuristicMapping($row);
            if ($mapping['date'] !== -1 && $mapping['amount'] !== -1) {
                $headerIndex = $index;
                break;
            }
        }

        $header = $data[$headerIndex];
        $map = $this->getHeuristicMapping($header);
        $rows = array_slice($data, $headerIndex + 1);
        $transactions = [];

        foreach ($rows as $row) {
            if (count($row) < 1) continue;
            
            $dateIdx = $map['date'];
            $amountIdx = $map['amount'];
            if ($dateIdx === -1 && $amountIdx === -1) continue;

            $dateRaw = ($dateIdx !== -1 && isset($row[$dateIdx])) ? $row[$dateIdx] : null;
            $amountRaw = ($amountIdx !== -1 && isset($row[$amountIdx])) ? $row[$amountIdx] : 0;
            
            if (!$dateRaw && !$amountRaw) continue;

            $parsedDate = ImportHelper::parseDate($dateRaw);
            $parsedAmount = ImportHelper::parseNumber($amountRaw);
            
            if (!$parsedDate && $parsedAmount == 0) continue;

            $rawType = $map['type'] > -1 ? ($row[$map['type']] ?? '') : '';
            $concept = 'Operación';
            if ($map['ticker'] > -1 && isset($row[$map['ticker']])) {
                $concept = $row[$map['ticker']] ?: 'Operación';
            }

            $tx = [
                'date' => $parsedDate,
                'ticker' => $concept,
                'name' => $concept,
                'type' => ImportHelper::normalizeType($rawType, $parsedAmount, $concept),
                'quantity' => $map['quantity'] > -1 ? ImportHelper::parseNumber($row[$map['quantity']] ?? 0) : 0,
                'price_per_unit' => $map['price'] > -1 ? ImportHelper::parseNumber($row[$map['price']] ?? 0) : 0,
                'amount' => abs($parsedAmount),
                'category_name' => $map['category'] > -1 ? ($row[$map['category']] ?? null) : null,
                'description' => $map['description'] > -1 ? ($row[$map['description']] ?? null) : null,
            ];

            $transactions[] = $tx;
        }

        return $transactions;
    }

    /**
     * Mapeo heurístico de columnas basado en nombres comunes.
     */
    private function getHeuristicMapping($header)
    {
        $map = [
            'date' => -1, 
            'ticker' => -1, 
            'type' => -1, 
            'quantity' => -1, 
            'price' => -1, 
            'amount' => -1, 
            'category' => -1,
            'description' => -1
        ];

        foreach ($header as $index => $col) {
            $col = mb_strtolower(trim($col));
            
            // Fecha
            if (str_contains($col, 'date') || str_contains($col, 'fecha')) $map['date'] = $index;
            
            // Concepto / Nombre
            // Prioridad específica: "nombre" como concepto principal
            if ($col === 'nombre') {
                $map['ticker'] = $index;
            } elseif ($map['ticker'] === -1) {
                if (preg_match('/\b(ticker|symbol|activo|isin|name|concepto|detalle|descrip)\b/u', $col)) {
                    // Evitamos que coincida con "detalles" (plural) si queremos que sea descripción
                    if ($col !== 'detalles') {
                        $map['ticker'] = $index;
                    }
                }
            }
            
            // Descripción / Nota
            if ($col === 'detalles' || $col === 'nota' || $col === 'notas' || $col === 'observaciones') {
                $map['description'] = $index;
            }
            
            // Tipo
            if (str_contains($col, 'type') || str_contains($col, 'tipo')) $map['type'] = $index;
            
            // Cantidad
            if (str_contains($col, 'quantity') || str_contains($col, 'cantidad')) $map['quantity'] = $index;
            
            // Precio
            if (str_contains($col, 'price') || str_contains($col, 'precio')) $map['price'] = $index;
            
            // Importe / Valor
            if (str_contains($col, 'amount') || str_contains($col, 'total') || str_contains($col, 'valor') || str_contains($col, 'importe')) $map['amount'] = $index;
            
            // Categoría
            if (str_contains($col, 'category') || preg_match('/\bcategorí?a\b/u', $col)) $map['category'] = $index;
        }
        
        return $map;
    }
}
