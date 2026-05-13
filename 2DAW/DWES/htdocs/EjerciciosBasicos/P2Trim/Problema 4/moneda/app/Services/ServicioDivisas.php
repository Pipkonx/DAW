<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ServicioDivisas
{
    /**
     * Obtiene el cambio de una moneda a EUR.
     * Usamos la API de @fawazahmed0/currency-api
     */
    public function convertToEur(float $amount, string $fromCurrency): ?float
    {
        $fromCurrency = strtolower($fromCurrency);
        
        if ($fromCurrency === 'eur') {
            return $amount;
        }

        try {
            // Documentación API: https://github.com/fawazahmed0/currency-api
            $response = Http::get("https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/{$fromCurrency}.json");

            if ($response->successful()) {
                $data = $response->json();
                $rate = $data[$fromCurrency]['eur'] ?? null;

                if ($rate) {
                    return $amount * $rate;
                }
            }
        } catch (\Exception $e) {
            // Registrar error o gestionarlo
            return null;
        }

        return null;
    }

    /**
     * Obtiene la lista completa de monedas disponibles desde la API.
     */
    public function getCurrencies(): array
    {
        try {
            $response = Http::get("https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies.json");
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            return [];
        }
        return [];
    }
}
