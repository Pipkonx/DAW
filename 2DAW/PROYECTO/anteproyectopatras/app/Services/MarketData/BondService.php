<?php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BondService
{
    private $apiKey;
    private $baseUrl = 'https://eodhd.com/api';

    public function __construct()
    {
        $this->apiKey = env('EODHD_API_KEY');
    }

    public function getPrice($isin)
    {
        return Cache::remember("bond_price_{$isin}", 3600, function () use ($isin) {
            try {
                if (empty($this->apiKey)) {
                    return $this->getMockPrice($isin);
                }

                $response = Http::get("{$this->baseUrl}/real-time/{$isin}?api_token={$this->apiKey}&fmt=json");
                
                if ($response->successful()) {
                    return $response->json()['close'] ?? null;
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error("Error fetching bond price for {$isin}: " . $e->getMessage());
                return null;
            }
        });
    }

    public function search($query)
    {
        return Cache::remember("bond_search_{$query}", 86400, function () use ($query) {
            try {
                if (empty($this->apiKey)) {
                    return $this->getMockSearch($query);
                }

                $response = Http::get("{$this->baseUrl}/search/{$query}?api_token={$this->apiKey}&type=bond&fmt=json");

                if ($response->successful()) {
                    return $response->json();
                }

                return [];
            } catch (\Exception $e) {
                Log::error("Error searching bonds for {$query}: " . $e->getMessage());
                return [];
            }
        });
    }

    public function getHistoricalPrice($isin, $date)
    {
        return Cache::remember("bond_price_{$isin}_{$date}", 86400, function () use ($isin, $date) {
            try {
                if (empty($this->apiKey)) {
                    return $this->getMockPrice($isin);
                }

                $response = Http::get("{$this->baseUrl}/eod/{$isin}?from={$date}&to={$date}&api_token={$this->apiKey}&fmt=json");
                
                if ($response->successful() && !empty($response->json())) {
                    return $response->json()[0]['close'];
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error("Error fetching historical bond price for {$isin} on {$date}: " . $e->getMessage());
                return null;
            }
        });
    }

    private function getMockPrice($isin)
    {
        return 98.50 + (rand(0, 300) / 100);
    }

    private function getMockSearch($query)
    {
        return [
            [
                'Code' => 'US10Y',
                'Name' => 'US 10 Year Treasury Note',
                'Exchange' => 'US Government',
                'Type' => 'Bond',
            ],
            [
                'Code' => 'DE10Y',
                'Name' => 'Germany 10 Year Government Bond',
                'Exchange' => 'Germany',
                'Type' => 'Bond',
            ],
        ];
    }
}
