<?php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\ApiService;
use Carbon\Carbon;

/**
 * StockPriceService - Motor de cotizaciones en tiempo real.
 * 
 * Gestiona el flujo de precios de activos financieros, integrando 
 * proveedores externos (FMP) y sistemas de caché de alta velocidad.
 */
class StockPriceService
{
    private $apiKey;
    private $baseUrl = 'https://financialmodelingprep.com/api/v3';
    private $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiKey = config('services.fmp.key') ?? env('FMP_API_KEY');
        $this->apiService = $apiService;
    }

    /**
     * Obtiene el precio actual de un activo.
     */
    public function getPrice(string $symbol)
    {
        $symbol = strtoupper(trim($symbol));
        return Cache::remember("stock_price_{$symbol}", 600, function () use ($symbol) {
            if (empty($this->apiKey)) return $this->getMockPrice($symbol);

            try {
                $response = Http::get("{$this->baseUrl}/quote/{$symbol}?apikey={$this->apiKey}");
                if ($response->successful() && !empty($response->json())) {
                    $this->apiService->trackRequest('FMP');
                    return (float) $response->json()[0]['price'];
                }
            } catch (\Exception $e) {
                Log::error("Error en precio {$symbol}: " . $e->getMessage());
            }
            return $this->getMockPrice($symbol);
        });
    }

    /**
     * Recupera el precio de un activo en una fecha histórica específica.
     */
    public function getHistoricalPrice(string $symbol, string $date)
    {
        return Cache::remember("stock_price_{$symbol}_{$date}", 86400, function () use ($symbol, $date) {
            if (empty($this->apiKey)) return $this->getMockPrice($symbol);

            try {
                $response = Http::get("{$this->baseUrl}/historical-price-full/{$symbol}?from={$date}&to={$date}&apikey={$this->apiKey}");
                if ($response->successful() && !empty($response->json()['historical'])) {
                    $this->apiService->trackRequest('FMP');
                    return (float) $response->json()['historical'][0]['close'];
                }
            } catch (\Exception $e) {
                Log::error("Error precio histórico {$symbol}: " . $e->getMessage());
            }
            return null;
        });
    }

    /**
     * Genera la serie de datos históricos para la renderización de gráficos.
     */
    public function getHistoricalData(string $symbol, int $days = 365)
    {
        $to = now()->format('Y-m-d');
        $from = now()->subDays($days)->format('Y-m-d');

        return Cache::remember("stock_history_{$symbol}_{$days}", 3600, function () use ($symbol, $from, $to) {
            if (empty($this->apiKey)) return $this->getMockHistory($symbol, $from, $to);

            try {
                $response = Http::get("{$this->baseUrl}/historical-price-full/{$symbol}?from={$from}&to={$to}&apikey={$this->apiKey}");
                if ($response->successful() && isset($response->json()['historical'])) {
                    $this->apiService->trackRequest('FMP');
                    return array_reverse($response->json()['historical']);
                }
            } catch (\Exception $e) {
                Log::error("Error serie histórica {$symbol}: " . $e->getMessage());
            }
            return $this->getMockHistory($symbol, $from, $to);
        });
    }

    /**
     * Recupera los activos con mayor revaloración del día.
     */
    public function getTopGainers(): array
    {
        return Cache::remember("stock_market_gainers", 3600, function () {
            if (empty($this->apiKey)) return $this->getMockValues('gainers');
            $response = Http::get("{$this->baseUrl}/stock_market/gainers?apikey={$this->apiKey}");
            return $response->successful() ? $response->json() : $this->getMockValues('gainers');
        });
    }

    /**
     * Recupera los activos con mayor caída de la jornada.
     */
    public function getTopLosers(): array
    {
        return Cache::remember("stock_market_losers", 3600, function () {
            if (empty($this->apiKey)) return $this->getMockValues('losers');
            $response = Http::get("{$this->baseUrl}/stock_market/losers?apikey={$this->apiKey}");
            return $response->successful() ? $response->json() : $this->getMockValues('losers');
        });
    }

    /**
     * Recupera los activos con mayor volumen de negociación (Más activos).
     */
    public function getMostActive(): array
    {
        return Cache::remember("stock_market_active", 3600, function () {
            if (empty($this->apiKey)) return $this->getMockValues('actively_traded');
            $response = Http::get("{$this->baseUrl}/stock_market/actively_traded?apikey={$this->apiKey}");
            return $response->successful() ? $response->json() : $this->getMockValues('actively_traded');
        });
    }

    // --- MÉTODOS DE SIMULACIÓN (MOCKS) ---

    private function getMockPrice($symbol)
    {
        $prices = ['AAPL' => 175.50, 'MSFT' => 402.10, 'GOOGL' => 145.30, 'TSLA' => 190.20, 'NVDA' => 880.40];
        return $prices[strtoupper($symbol)] ?? rand(10, 500) + (rand(0, 99) / 100);
    }

    private function getMockHistory($symbol, $from, $to)
    {
        $data = [];
        $current = Carbon::parse($from);
        $end = Carbon::parse($to);
        $price = $this->getMockPrice($symbol);

        while ($current->lte($end)) {
            if (!$current->isWeekend()) {
                $price += (rand(-200, 210) / 100);
                $data[] = ['date' => $current->format('Y-m-d'), 'close' => round($price, 2), 'volume' => rand(1000000, 5000000)];
            }
            $current->addDay();
        }
        return $data;
    }

    private function getMockValues(string $type): array
    {
        if ($type === 'gainers') {
            return [
                ['symbol' => 'NVDA', 'name' => 'NVIDIA', 'price' => 880.4, 'changesPercentage' => 3.5, 'image' => 'https://financialmodelingprep.com/image-stock/NVDA.png'],
                ['symbol' => 'META', 'name' => 'Meta Platforms', 'price' => 495.2, 'changesPercentage' => 2.8, 'image' => 'https://financialmodelingprep.com/image-stock/META.png'],
                ['symbol' => 'AMZN', 'name' => 'Amazon.com', 'price' => 175.4, 'changesPercentage' => 1.9, 'image' => 'https://financialmodelingprep.com/image-stock/AMZN.png'],
                ['symbol' => 'MSFT', 'name' => 'Microsoft', 'price' => 415.2, 'changesPercentage' => 1.2, 'image' => 'https://financialmodelingprep.com/image-stock/MSFT.png'],
                ['symbol' => 'GOOGL', 'name' => 'Alphabet Inc.', 'price' => 152.8, 'changesPercentage' => 0.8, 'image' => 'https://financialmodelingprep.com/image-stock/GOOGL.png']
            ];
        } elseif ($type === 'losers') {
            return [
                ['symbol' => 'TSLA', 'name' => 'Tesla, Inc.', 'price' => 172.5, 'changesPercentage' => -4.2, 'image' => 'https://financialmodelingprep.com/image-stock/TSLA.png'],
                ['symbol' => 'NFLX', 'name' => 'Netflix, Inc.', 'price' => 610.1, 'changesPercentage' => -2.5, 'image' => 'https://financialmodelingprep.com/image-stock/NFLX.png'],
                ['symbol' => 'PYPL', 'name' => 'PayPal Holdings', 'price' => 62.4, 'changesPercentage' => -2.1, 'image' => 'https://financialmodelingprep.com/image-stock/PYPL.png'],
                ['symbol' => 'INTC', 'name' => 'Intel Corp', 'price' => 42.8, 'changesPercentage' => -1.8, 'image' => 'https://financialmodelingprep.com/image-stock/INTC.png'],
                ['symbol' => 'BA', 'name' => 'Boeing Co', 'price' => 182.2, 'changesPercentage' => -1.1, 'image' => 'https://financialmodelingprep.com/image-stock/BA.png']
            ];
        }
        
        return [
            ['symbol' => 'AAPL', 'name' => 'Apple Inc.', 'price' => 170.2, 'changesPercentage' => 0.5, 'volume' => 50000000, 'image' => 'https://financialmodelingprep.com/image-stock/AAPL.png'],
            ['symbol' => 'BRK-B', 'name' => 'Berkshire Hathaway', 'price' => 405.1, 'changesPercentage' => -0.2, 'volume' => 30000000, 'image' => 'https://financialmodelingprep.com/image-stock/BRK-B.png'],
            ['symbol' => 'V', 'name' => 'Visa Inc.', 'price' => 280.4, 'changesPercentage' => 0.3, 'volume' => 12000000, 'image' => 'https://financialmodelingprep.com/image-stock/V.png'],
            ['symbol' => 'JPM', 'name' => 'JPMorgan Chase', 'price' => 188.2, 'changesPercentage' => 0.1, 'volume' => 15000000, 'image' => 'https://financialmodelingprep.com/image-stock/JPM.png'],
            ['symbol' => 'DIS', 'name' => 'Walt Disney Co', 'price' => 112.5, 'changesPercentage' => -0.5, 'volume' => 18000000, 'image' => 'https://financialmodelingprep.com/image-stock/DIS.png']
        ];
    }
}
