<?php

namespace App\Services\Financial\Import;

use Carbon\Carbon;

class ImportHelper
{
    /**
     * Parsea un string a un número flotante, manejando formatos europeos y americanos.
     */
    public static function parseNumber($str)
    {
        if (is_numeric($str)) return (float) $str;
        $str = preg_replace('/[^\d,.-]/', '', trim((string)$str));
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

    /**
     * Parsea una fecha desde un string reconociendo formatos comunes.
     */
    public static function parseDate($str)
    {
        if (empty($str)) return null;
        
        $str = trim(preg_replace('/[^\d\/\-\.]/', ' ', $str));
        $parts = preg_split('/\s+/', $str);
        $str = $parts[0]; 

        try { 
            if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{2,4})$/', $str, $matches)) {
                $day = $matches[1];
                $month = $matches[2];
                $year = $matches[3];
                if (strlen($year) === 2) $year = "20" . $year;
                return Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
            }
            return Carbon::parse($str)->format('Y-m-d'); 
        } catch (\Exception $e) { 
            return null; 
        }
    }

    /**
     * Normaliza el tipo de transacción basándose en palabras clave y el importe.
     */
    public static function normalizeType($typeStr, $amount = 0, $description = '')
    {
        $combined = mb_strtolower($typeStr . ' ' . $description);
        
        // Prioridad 0: Detectar si el nombre parece un activo de inversión conocido
        // Esto permite capturar fondos, ETFs y criptos por su nombre comercial
        $assetTypeGuess = self::guessAssetType($description);
        if (in_array($assetTypeGuess, ['fund', 'etf', 'crypto'])) {
            if (preg_match('/\b(vende|venta|sell)\b/u', $combined)) return 'sell';
            if (preg_match('/\b(dividendo|dividend)\b/u', $combined)) return 'dividend';
            return 'buy';
        }

        // Prioridad 1: Palabras clave de inversión explícitas
        $investmentKeywords = [
            'ticker', 'isin', 'dividend', 'dividendo', 'stock', 'acción', 'accion',
            'etf', 'crypto', 'cripto', 'fund', 'fondo de inversión',
            'msci', 's&p', 's&p 500', 'nasdaq', 'dow jones',
            'bitcoin', 'btc', 'eth', 'ethereum', 'solana', 'cardano', 'ripple', 'xrp',
            'fidelity', 'vanguard', 'blackrock', 'ishares', 'amundi', 'lyxor',
            'renta variable', 'renta fija', 'bono', 'bond',
        ];
        $isInvestment = false;
        foreach ($investmentKeywords as $key) {
            if (str_contains($combined, $key)) {
                $isInvestment = true;
                break;
            }
        }

        if ($isInvestment) {
            if (preg_match('/\b(vende|venta|sell)\b/u', $combined)) return 'sell';
            if (preg_match('/\b(dividendo|dividend)\b/u', $combined)) return 'dividend';
            return 'buy';
        }

        // Prioridad 2: Signo del importe para gastos/ingresos comunes
        if ($amount > 0) {
            if (str_contains($combined, 'pago') || str_contains($combined, 'gasto')) return 'expense';
            return 'income';
        } elseif ($amount < 0) {
            if (preg_match('/\b(devolución|devolucion|abono)\b/u', $combined)) return 'income';
            return 'expense';
        }

        // Prioridad 3: Palabras clave para gastos comunes
        if (preg_match('/\b(recibido|ingreso|income|abono|nómina|nomina|bizum recibido)\b/u', $combined)) return 'income';
        if (preg_match('/\b(compra|pago|gasto|expense|bizum enviado|recibo|adeudo)\b/u', $combined)) return 'expense';

        return 'expense';
    }

    /**
     * Intenta adivinar el tipo de activo basándose en su nombre.
     */
    public static function guessAssetType($name)
    {
        if (preg_match('/(Fondo|Fund|Index|Indice|Acc|Clase|Sicav)/i', $name)) return 'fund';
        if (stripos($name, 'ETF') !== false) return 'etf';
        if (preg_match('/(Bitcoin|BTC|ETH|Crypto)/i', $name)) return 'crypto';
        return 'stock';
    }
}
