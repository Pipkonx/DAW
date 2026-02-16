<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketAsset;
use Carbon\Carbon;

class MarketDataController extends Controller
{
    /**
     * Search for assets by ticker, name, or ISIN.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $assets = MarketAsset::where('ticker', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->orWhere('isin', 'like', "%{$query}%")
            ->take(10)
            ->get();

        return response()->json($assets);
    }

    /**
     * Get historical price for an asset on a specific date.
     * Includes both MOCKED implementation (demo) and REAL API implementation (commented).
     */
    public function getPrice(Request $request)
    {
        $request->validate([
            'ticker' => 'required|string',
            'date' => 'required|date',
            'type' => 'nullable|string'
        ]);

        $ticker = strtoupper($request->ticker);
        $date = Carbon::parse($request->date);
        
        // --- OPCIÓN 1: IMPLEMENTACIÓN REAL CON API (Ej. Yahoo Finance / Finnhub / AlphaVantage) ---
        // Para usar esto, necesitas instalar Guzzle: composer require guzzlehttp/guzzle
        /*
        try {
            // Ejemplo con Yahoo Finance (No oficial, pero gratuito para pruebas)
            // URL: https://query1.finance.yahoo.com/v8/finance/chart/{TICKER}?period1={TIMESTAMP}&period2={TIMESTAMP}&interval=1d
            
            $timestamp = $date->timestamp;
            // Pedimos un rango pequeño alrededor de la fecha (el día anterior y el siguiente) para asegurar datos
            $period1 = $date->copy()->subDays(2)->timestamp; 
            $period2 = $date->copy()->addDays(1)->timestamp;

            $client = new \GuzzleHttp\Client();
            $response = $client->get("https://query1.finance.yahoo.com/v8/finance/chart/{$ticker}", [
                'query' => [
                    'period1' => $period1,
                    'period2' => $period2,
                    'interval' => '1d'
                ],
                'verify' => false // Solo para desarrollo local si hay problemas de SSL
            ]);

            $data = json_decode($response->getBody(), true);
            
            if (!empty($data['chart']['result'][0]['meta']['regularMarketPrice'])) {
                // Precio actual si la fecha es hoy
                 $price = $data['chart']['result'][0]['meta']['regularMarketPrice'];
            } elseif (!empty($data['chart']['result'][0]['indicators']['quote'][0]['close'])) {
                // Precio histórico (el más cercano)
                $closes = $data['chart']['result'][0]['indicators']['quote'][0]['close'];
                // Filtramos nulos y tomamos el último disponible en el rango
                $validCloses = array_filter($closes);
                $price = end($validCloses);
            }
            
            if (isset($price)) {
                 return response()->json([
                    'ticker' => $ticker,
                    'date' => $date->format('Y-m-d'),
                    'price' => round($price, 2),
                    'currency' => $data['chart']['result'][0]['meta']['currency'] ?? 'USD',
                    'source' => 'Yahoo Finance API'
                ]);
            }
        } catch (\Exception $e) {
            // Si falla la API, hacemos fallback a la simulación o devolvemos error
            \Log::error("API Error: " . $e->getMessage());
        }
        */

        // --- OPCIÓN 2: SIMULACIÓN INTELIGENTE (Para Demo sin API Key) ---
        
        // 1. Determine Base Price based on Ticker
        $basePrice = $this->getBasePrice($ticker);
        
        // 2. Calculate variability based on date (deterministic chaos)
        // We use timestamp to create a "trend"
        $daysDiff = $date->diffInDays(Carbon::now()->subYears(5)); // Time since 5 years ago
        
        // Create a trend: generally going up 7% a year
        $growthFactor = pow(1.07, $daysDiff / 365);
        
        // Add seasonality/volatility (sine wave)
        $seasonality = sin($daysDiff / 30) * 0.05; // 5% monthly swing
        
        // Add "random" noise based on date hash
        $seed = crc32($date->format('Y-m-d') . $ticker);
        srand($seed);
        $noise = (rand(0, 100) / 1000) - 0.05; // +/- 5% noise
        
        $price = $basePrice * $growthFactor * (1 + $seasonality + $noise);
        
        // Ensure price isn't negative
        $price = max($price, 0.01);

        return response()->json([
            'ticker' => $ticker,
            'date' => $date->format('Y-m-d'),
            'price' => round($price, 2),
            'currency' => 'USD', // Default
            'source' => 'Simulated Market Data (Demo)'
        ]);
    }

    private function getBasePrice($ticker)
    {
        // Define some known base prices (approximate values from 5 years ago)
        $bases = [
            'BTC' => 20000,
            'ETH' => 1000,
            'SOL' => 20,
            'AAPL' => 100,
            'MSFT' => 200,
            'TSLA' => 150,
            'NVDA' => 50,
            'SPY' => 300,
            'VUSA' => 50,
            'GOLD' => 1700,
            'EUR' => 1.10,
        ];

        return $bases[$ticker] ?? 100; // Default 100 if unknown
    }
}
