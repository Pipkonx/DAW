<?php

namespace App\Services\Financial;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FMPService
{
    protected $apiKey;
    protected $baseUrl = 'https://financialmodelingprep.com/api/v3';

    public function __construct()
    {
        $this->apiKey = env('FMP_API_KEY');
    }

    public function search(string $query)
    {
        // Cache search results for 1 day
        return Cache::remember("fmp_search_{$query}", 86400, function () use ($query) {
            if (empty($this->apiKey)) return [];

            try {
                $response = Http::get("{$this->baseUrl}/search", [
                    'query' => $query,
                    'limit' => 10,
                    'apikey' => $this->apiKey
                ]);

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                Log::error("FMP API Search Error: " . $e->getMessage());
            }
            return [];
        });
    }

    public function getProfile(string $ticker)
    {
        // Cache profile (logo, sector, etc) for 30 days
        return Cache::remember("fmp_profile_{$ticker}", 2592000, function () use ($ticker) {
            if (empty($this->apiKey)) return null;

            try {
                $response = Http::get("{$this->baseUrl}/profile/{$ticker}", [
                    'apikey' => $this->apiKey
                ]);

                if ($response->successful() && !empty($response->json())) {
                    return $response->json()[0];
                }
            } catch (\Exception $e) {
                Log::error("FMP API Profile Error: " . $e->getMessage());
            }
            return null;
        });
    }

    public function getPrice(string $ticker)
    {
        // Cache price for 30 minutes to save requests
        return Cache::remember("fmp_price_{$ticker}", 1800, function () use ($ticker) {
            if (empty($this->apiKey)) return null;

            try {
                $response = Http::get("{$this->baseUrl}/quote/{$ticker}", [
                    'apikey' => $this->apiKey
                ]);

                if ($response->successful() && !empty($response->json())) {
                    return $response->json()[0]['price'] ?? null;
                }
            } catch (\Exception $e) {
                Log::error("FMP API Price Error: " . $e->getMessage());
            }
            return null;
        });
    }

    public function getHistoricalPrice(string $ticker, string $date)
    {
        // Cache historical permanently
        return Cache::rememberForever("fmp_history_{$ticker}_{$date}", function () use ($ticker, $date) {
            if (empty($this->apiKey)) return null;

            try {
                // FMP historical daily
                $response = Http::get("{$this->baseUrl}/historical-price-full/{$ticker}", [
                    'from' => $date,
                    'to' => $date,
                    'apikey' => $this->apiKey
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if (!empty($data['historical'])) {
                        return $data['historical'][0]['close'] ?? null;
                    }
                }
            } catch (\Exception $e) {
                Log::error("FMP API Historical Error: " . $e->getMessage());
            }
            return null;
        });
    }
}
