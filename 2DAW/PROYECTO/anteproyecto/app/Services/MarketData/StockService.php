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
        $this->apiKey = env('FMP_API_KEY');
    }

    public function getPrice($symbol)
    {
        return Cache::remember("stock_price_{$symbol}", 600, function () use ($symbol) {
            try {
                // Check if API key is set, otherwise return mock data
                if (empty($this->apiKey)) {
                    return $this->getMockPrice($symbol);
                }

                $response = Http::get("{$this->baseUrl}/quote/{$symbol}?apikey={$this->apiKey}");
                
                if ($response->successful() && !empty($response->json())) {
                    return $response->json()[0]['price'];
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error("Error fetching stock price for {$symbol}: " . $e->getMessage());
                return null;
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
        ];

        return $prices[strtoupper($symbol)] ?? rand(10, 500) + (rand(0, 99) / 100);
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
