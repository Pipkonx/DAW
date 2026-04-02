<?php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StockService
{
    private $apiKey;
    private $baseUrl = 'https://financialmodelingprep.com/api/v3';

    public function __construct()
    {
        $this->apiKey = config('services.fmp.key') ?? env('FMP_API_KEY');
    }

    public function getPrice($symbol)
    {
        $symbol = strtoupper(trim($symbol));
        
        return Cache::remember("stock_price_{$symbol}", 600, function () use ($symbol) {
            try {
                // Check if API key is set, otherwise return mock data
                if (empty($this->apiKey)) {
                    Log::info("No FMP API Key, using mock data for {$symbol}");
                    return $this->getMockPrice($symbol);
                }

                $url = "{$this->baseUrl}/quote/{$symbol}?apikey={$this->apiKey}";
                $response = Http::get($url);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if (!empty($data) && isset($data[0]['price'])) {
                        return (float) $data[0]['price'];
                    }
                    
                    Log::warning("FMP API returned empty data for {$symbol}. URL: {$url}. Falling back to Mock.");
                } else {
                    Log::error("FMP API error for {$symbol}: " . $response->status() . " - " . $response->body() . ". Falling back to Mock.");
                }
                
                // Final fallback if API fails
                return $this->getMockPrice($symbol);
            } catch (\Exception $e) {
                Log::error("Exception in StockService for {$symbol}: " . $e->getMessage() . ". Falling back to Mock.");
                return $this->getMockPrice($symbol);
            }
        });
    }

    public function search($query)
    {
        return Cache::remember("stock_search_{$query}", 86400, function () use ($query) {
            try {
                if (empty($this->apiKey)) {
                    return $this->getMockSearch($query);
                }

                $response = Http::get("{$this->baseUrl}/search?query={$query}&limit=10&apikey={$this->apiKey}");

                if ($response->successful()) {
                    return $response->json();
                }

                return [];
            } catch (\Exception $e) {
                Log::error("Error searching stocks for {$query}: " . $e->getMessage());
                return [];
            }
        });
    }

    public function getHistoricalPrice($symbol, $date)
    {
        return Cache::remember("stock_price_{$symbol}_{$date}", 86400, function () use ($symbol, $date) {
            try {
                if (empty($this->apiKey)) {
                    return $this->getMockPrice($symbol); // Return random price for demo
                }

                $response = Http::get("{$this->baseUrl}/historical-price-full/{$symbol}?from={$date}&to={$date}&apikey={$this->apiKey}");
                
                if ($response->successful() && !empty($response->json()['historical'])) {
                    return $response->json()['historical'][0]['close'];
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error("Error fetching historical stock price for {$symbol} on {$date}: " . $e->getMessage());
                return null;
            }
        });
    }

    private function getMockPrice($symbol)
    {
        // Mock prices for demo purposes
        $prices = [
            'AAPL' => 175.50,
            'MSFT' => 402.10,
            'GOOGL' => 145.30,
            'TSLA' => 190.20,
            'AMZN' => 170.80,
            'NVDA' => 880.40,
            'META' => 495.20,
            'AMD'  => 185.30,
            'SPY'  => 515.20,
            'VOO'  => 472.10,
            'QQQ'  => 440.50,
            'IVV'  => 518.30,
            'VTI'  => 255.40,
            'SCHD' => 78.20,
            'JEPI' => 55.30,
            'ARKK' => 48.10,
            'NFLX' => 610.20,
            'PLTR' => 24.50,
        ];

        return $prices[strtoupper($symbol)] ?? rand(10, 500) + (rand(0, 99) / 100);
    }

    public function getTopGainers()
    {
        return Cache::remember("stock_market_gainers", 3600, function () {
            try {
                if (empty($this->apiKey)) return $this->getMockGainers();

                $response = Http::get("{$this->baseUrl}/stock_market/gainers?apikey={$this->apiKey}");
                if ($response->successful()) return $response->json();
                
                return $this->getMockGainers();
            } catch (\Exception $e) {
                Log::error("Error fetching stock gainers: " . $e->getMessage());
                return $this->getMockGainers();
            }
        });
    }

    public function getTopLosers()
    {
        return Cache::remember("stock_market_losers", 3600, function () {
            try {
                if (empty($this->apiKey)) return $this->getMockLosers();

                $response = Http::get("{$this->baseUrl}/stock_market/losers?apikey={$this->apiKey}");
                if ($response->successful()) return $response->json();
                
                return $this->getMockLosers();
            } catch (\Exception $e) {
                Log::error("Error fetching stock losers: " . $e->getMessage());
                return $this->getMockLosers();
            }
        });
    }

    public function getMostActive()
    {
        return Cache::remember("stock_market_actives", 3600, function () {
            try {
                if (empty($this->apiKey)) return $this->getMockActive();

                $response = Http::get("{$this->baseUrl}/stock_market/actives?apikey={$this->apiKey}");
                if ($response->successful()) return $response->json();
                
                return $this->getMockActive();
            } catch (\Exception $e) {
                Log::error("Error fetching most active stocks: " . $e->getMessage());
                return $this->getMockActive();
            }
        });
    }

    private function getMockGainers()
    {
        return [
            ['symbol' => 'NVDA', 'name' => 'NVIDIA', 'price' => 880.4, 'changesPercentage' => 3.5, 'change' => 30.2],
            ['symbol' => 'META', 'name' => 'Meta', 'price' => 495.2, 'changesPercentage' => 2.8, 'change' => 13.5],
            ['symbol' => 'AMD', 'name' => 'AMD', 'price' => 185.3, 'changesPercentage' => 2.1, 'change' => 3.8],
        ];
    }

    private function getMockLosers()
    {
        return [
            ['symbol' => 'TSLA', 'name' => 'Tesla', 'price' => 190.2, 'changesPercentage' => -4.2, 'change' => -8.3],
            ['symbol' => 'AAPL', 'name' => 'Apple', 'price' => 175.5, 'changesPercentage' => -1.1, 'change' => -1.9],
            ['symbol' => 'F', 'name' => 'Ford', 'price' => 12.1, 'changesPercentage' => -0.8, 'change' => -0.1],
        ];
    }

    private function getMockActive()
    {
        return [
            ['symbol' => 'MSFT', 'name' => 'Microsoft', 'price' => 402.1, 'changesPercentage' => 0.5, 'change' => 2.1],
            ['symbol' => 'PLTR', 'name' => 'Palantir', 'price' => 24.5, 'changesPercentage' => 1.2, 'change' => 0.3],
            ['symbol' => 'AMZN', 'name' => 'Amazon', 'price' => 180.5, 'changesPercentage' => 0.8, 'change' => 1.5],
        ];
    }

    private function getMockSearch($query)
    {
        return [
            [
                'symbol' => 'AAPL',
                'name' => 'Apple Inc.',
                'currency' => 'USD',
                'stockExchange' => 'NASDAQ',
                'exchangeShortName' => 'NASDAQ',
            ],
            [
                'symbol' => 'MSFT',
                'name' => 'Microsoft Corporation',
                'currency' => 'USD',
                'stockExchange' => 'NASDAQ',
                'exchangeShortName' => 'NASDAQ',
            ],
            [
                'symbol' => 'TSLA',
                'name' => 'Tesla Inc.',
                'currency' => 'USD',
                'stockExchange' => 'NASDAQ',
                'exchangeShortName' => 'NASDAQ',
            ],
        ];
    }
}
