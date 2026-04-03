<?php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

use App\Services\ApiService;

class CryptoService
{
    private $apiKey;
    private $baseUrl = 'https://api.coingecko.com/api/v3';
    private $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiKey = config('services.coingecko.key') ?? env('COINGECKO_API_KEY');
        $this->apiService = $apiService;
    }

    public function getPrice($identifier)
    {
        return Cache::remember("crypto_price_{$identifier}", 600, function () use ($identifier) {
            try {
                // If no API key, use mock data
                if (empty($this->apiKey)) {
                    return $this->getMockPrice($identifier);
                }

                // CoinGecko IDs are lowercase
                $id = strtolower($identifier);

                $response = Http::get("{$this->baseUrl}/simple/price?ids={$id}&vs_currencies=usd&x_cg_demo_api_key={$this->apiKey}");
                
                if ($response->successful() && isset($response->json()[$id]['usd'])) {
                    $this->apiService->trackRequest('CoinGecko');
                    return $response->json()[$id]['usd'];
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error("Error fetching crypto price for {$identifier}: " . $e->getMessage());
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
                    $this->apiService->trackRequest('CoinGecko');
                    $coins = $response->json()['coins'];
                    return array_map(function($coin) {
                        return [
                            'id' => $coin['id'], // This is the api_id
                            'symbol' => strtoupper($coin['symbol']),
                            'name' => $coin['name'],
                            'market_cap_rank' => $coin['market_cap_rank'] ?? null,
                            'thumb' => $coin['thumb'] ?? null,
                        ];
                    }, $coins);
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
                    $this->apiService->trackRequest('CoinGecko');
                    return $response->json()['market_data']['current_price']['usd'];
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error("Error fetching historical crypto price for {$symbol} on {$formattedDate}: " . $e->getMessage());
                return null;
            }
        });
    }

    public function getChartData($identifier, $days = 365)
    {
        $id = strtolower($identifier);
        return Cache::remember("crypto_chart_{$id}_{$days}", 3600, function () use ($id, $days) {
            try {
                if (empty($this->apiKey)) {
                    return $this->getMockHistory($id, $days);
                }

                $response = Http::get("{$this->baseUrl}/coins/{$id}/market_chart?vs_currency=usd&days={$days}&interval=daily&x_cg_demo_api_key={$this->apiKey}");
                
                if ($response->successful()) {
                    $this->apiService->trackRequest('CoinGecko');
                    $prices = $response->json()['prices'];
                    return array_map(function($p) {
                        return [
                            'date' => date('Y-m-d', $p[0] / 1000),
                            'close' => $p[1]
                        ];
                    }, $prices);
                }
                
                return $this->getMockHistory($id, $days);
            } catch (\Exception $e) {
                Log::error("Error fetching crypto chart for {$id}: " . $e->getMessage());
                return $this->getMockHistory($id, $days);
            }
        });
    }

    private function getMockHistory($id, $days)
    {
        $data = [];
        $currentPrice = $this->getMockPrice($id);
        
        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $change = (rand(-300, 310) / 100);
            $currentPrice += ($currentPrice * ($change / 100));

            $data[] = [
                'date' => $date->format('Y-m-d'),
                'close' => round($currentPrice, 2)
            ];
        }
        return $data;
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

    public function getTrendingCoins()
    {
        return Cache::remember("crypto_trending", 3600, function () {
            try {
                if (empty($this->apiKey)) return $this->getMockTrending();

                $response = Http::get("{$this->baseUrl}/search/trending?x_cg_demo_api_key={$this->apiKey}");
                if ($response->successful()) {
                    $this->apiService->trackRequest('CoinGecko');
                    $coins = $response->json()['coins'];
                    return array_map(function($item) {
                        $coin = $item['item'];
                        return [
                            'id' => $coin['id'],
                            'symbol' => strtoupper($coin['symbol']),
                            'name' => $coin['name'],
                            'price' => $coin['data']['price'] ?? 0,
                            'change_percent' => floatval($coin['data']['price_change_percentage_24h']['usd'] ?? 0),
                            'image' => $coin['small'] ?? $coin['large'] ?? null,
                        ];
                    }, $coins);
                }
                
                return $this->getMockTrending();
            } catch (\Exception $e) {
                Log::error("Error fetching trending crypto: " . $e->getMessage());
                return $this->getMockTrending();
            }
        });
    }

    public function getTopByMarketCap($limit = 10)
    {
        return Cache::remember("crypto_top_mcap_{$limit}", 3600, function () use ($limit) {
            try {
                if (empty($this->apiKey)) return $this->getMockTrending();

                $response = Http::get("{$this->baseUrl}/coins/markets?vs_currency=usd&order=market_cap_desc&per_page={$limit}&page=1&sparkline=false&x_cg_demo_api_key={$this->apiKey}");
                
                if ($response->successful()) {
                    $this->apiService->trackRequest('CoinGecko');
                    return array_map(function($coin) {
                        return [
                            'id' => $coin['id'],
                            'symbol' => strtoupper($coin['symbol']),
                            'name' => $coin['name'],
                            'price' => floatval($coin['current_price']),
                            'change_percent' => floatval($coin['price_change_percentage_24h'] ?? 0),
                            'image' => $coin['image'] ?? null,
                        ];
                    }, $response->json());
                }
                
                return $this->getMockTrending();
            } catch (\Exception $e) {
                Log::error("Error fetching top crypto: " . $e->getMessage());
                return $this->getMockTrending();
            }
        });
    }

    private function getMockTrending()
    {
        return [
            ['id' => 'bitcoin', 'symbol' => 'BTC', 'name' => 'Bitcoin', 'price' => 66000, 'change_percent' => 2.5],
            ['id' => 'ethereum', 'symbol' => 'ETH', 'name' => 'Ethereum', 'price' => 3500, 'change_percent' => 1.8],
            ['id' => 'solana', 'symbol' => 'SOL', 'name' => 'Solana', 'price' => 180, 'change_percent' => 5.2],
        ];
    }
}
