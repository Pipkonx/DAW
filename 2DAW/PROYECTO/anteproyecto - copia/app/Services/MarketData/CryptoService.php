<?php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CryptoService
{
    private $apiKey;
    private $baseUrl = 'https://api.coingecko.com/api/v3';

    public function __construct()
    {
        $this->apiKey = env('COINGECKO_API_KEY');
    }

    public function getPrice($symbol)
    {
        return Cache::remember("crypto_price_{$symbol}", 600, function () use ($symbol) {
            try {
                // If no API key, use mock data
                if (empty($this->apiKey)) {
                    return $this->getMockPrice($symbol);
                }

                $response = Http::get("{$this->baseUrl}/simple/price?ids={$symbol}&vs_currencies=usd&x_cg_demo_api_key={$this->apiKey}");
                
                if ($response->successful() && isset($response->json()[$symbol]['usd'])) {
                    return $response->json()[$symbol]['usd'];
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error("Error fetching crypto price for {$symbol}: " . $e->getMessage());
                return null;
            }
        });
    }

    public function search($query)
    {
        return Cache::remember("crypto_search_{$query}", 86400, function () use ($query) {
            try {
                if (empty($this->apiKey)) {
                    return $this->getMockSearch($query);
                }

                $response = Http::get("{$this->baseUrl}/search?query={$query}&x_cg_demo_api_key={$this->apiKey}");

                if ($response->successful()) {
                    return $response->json()['coins'];
                }

                return [];
            } catch (\Exception $e) {
                Log::error("Error searching crypto for {$query}: " . $e->getMessage());
                return [];
            }
        });
    }

    public function getHistoricalPrice($symbol, $date)
    {
        // CoinGecko expects dd-mm-yyyy
        $formattedDate = date('d-m-Y', strtotime($date));
        
        return Cache::remember("crypto_price_{$symbol}_{$formattedDate}", 86400, function () use ($symbol, $formattedDate) {
            try {
                if (empty($this->apiKey)) {
                    return $this->getMockPrice($symbol);
                }

                $response = Http::get("{$this->baseUrl}/coins/{$symbol}/history?date={$formattedDate}&x_cg_demo_api_key={$this->apiKey}");
                
                if ($response->successful() && isset($response->json()['market_data']['current_price']['usd'])) {
                    return $response->json()['market_data']['current_price']['usd'];
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error("Error fetching historical crypto price for {$symbol} on {$formattedDate}: " . $e->getMessage());
                return null;
            }
        });
    }

    private function getMockPrice($symbol)
    {
        $prices = [
            'bitcoin' => 52000.50,
            'ethereum' => 2800.20,
            'solana' => 110.10,
            'cardano' => 0.60,
        ];

        return $prices[strtolower($symbol)] ?? rand(1, 1000) + (rand(0, 99) / 100);
    }

    private function getMockSearch($query)
    {
        return [
            [
                'id' => 'bitcoin',
                'name' => 'Bitcoin',
                'symbol' => 'BTC',
                'market_cap_rank' => 1,
            ],
            [
                'id' => 'ethereum',
                'name' => 'Ethereum',
                'symbol' => 'ETH',
                'market_cap_rank' => 2,
            ],
            [
                'id' => 'solana',
                'name' => 'Solana',
                'symbol' => 'SOL',
                'market_cap_rank' => 5,
            ],
        ];
    }
}
