<?php

namespace App\Services\Financial;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CoinGeckoService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.coingecko.com/api/v3';

    public function __construct()
    {
        $this->apiKey = env('COINGECKO_API_KEY');
    }

    public function search(string $query)
    {
        return Cache::remember("cg_search_{$query}", 86400, function () use ($query) {
            try {
                $params = ['query' => $query];
                if ($this->apiKey) $params['x_cg_demo_api_key'] = $this->apiKey;

                $response = Http::get("{$this->baseUrl}/search", $params);

                if ($response->successful()) {
                    return $response->json()['coins'] ?? [];
                }
            } catch (\Exception $e) {
                Log::error("CoinGecko Search Error: " . $e->getMessage());
            }
            return [];
        });
    }

    public function getPrice(string $coinId)
    {
        // Cache 10 minutes
        return Cache::remember("cg_price_{$coinId}", 600, function () use ($coinId) {
            try {
                $params = ['ids' => $coinId, 'vs_currencies' => 'usd,eur'];
                if ($this->apiKey) $params['x_cg_demo_api_key'] = $this->apiKey;

                $response = Http::get("{$this->baseUrl}/simple/price", $params);

                if ($response->successful()) {
                    return $response->json()[$coinId] ?? null;
                }
            } catch (\Exception $e) {
                Log::error("CoinGecko Price Error: " . $e->getMessage());
            }
            return null;
        });
    }

    public function getHistoricalPrice(string $coinId, string $date)
    {
        // Format date to dd-mm-yyyy for CoinGecko
        $cgDate = Carbon::parse($date)->format('d-m-Y');

        return Cache::rememberForever("cg_history_{$coinId}_{$date}", function () use ($coinId, $cgDate) {
            try {
                $params = ['date' => $cgDate];
                if ($this->apiKey) $params['x_cg_demo_api_key'] = $this->apiKey;

                $response = Http::get("{$this->baseUrl}/coins/{$coinId}/history", $params);

                if ($response->successful()) {
                    $data = $response->json();
                    // Return USD and EUR price
                    return [
                        'usd' => $data['market_data']['current_price']['usd'] ?? null,
                        'eur' => $data['market_data']['current_price']['eur'] ?? null
                    ];
                }
            } catch (\Exception $e) {
                Log::error("CoinGecko History Error: " . $e->getMessage());
            }
            return null;
        });
    }
}
