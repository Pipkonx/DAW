<?php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\ApiService;

class StockService
{
    private $apiKey;
    private $baseUrl = 'https://financialmodelingprep.com/api/v3';
    private $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiKey = config('services.fmp.key') ?? env('FMP_API_KEY');
        $this->apiService = $apiService;
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
                    $this->apiService->trackRequest('FMP');
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
                    $this->apiService->trackRequest('FMP');
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
                    $this->apiService->trackRequest('FMP');
                    return $response->json()['historical'][0]['close'];
                }
                
                return null;
            } catch (\Exception $e) {
                Log::error("Error fetching historical stock price for {$symbol} on {$date}: " . $e->getMessage());
                return null;
            }
        });
    }

    public function getHistoricalData($symbol, $days = 365)
    {
        $symbol = strtoupper(trim($symbol));
        $to = now()->format('Y-m-d');
        $from = now()->subDays($days)->format('Y-m-d');

        return Cache::remember("stock_history_{$symbol}_{$days}", 3600, function () use ($symbol, $from, $to) {
            try {
                if (empty($this->apiKey)) {
                    return $this->getMockHistory($symbol, $from, $to);
                }

                $url = "{$this->baseUrl}/historical-price-full/{$symbol}?from={$from}&to={$to}&apikey={$this->apiKey}";
                $response = Http::get($url);

                if ($response->successful()) {
                    $this->apiService->trackRequest('FMP');
                    $data = $response->json();
                    if (isset($data['historical'])) {
                        return array_reverse($data['historical']); // Ascending order for charts
                    }
                }
                return $this->getMockHistory($symbol, $from, $to);
            } catch (\Exception $e) {
                Log::error("Error fetching historical data for {$symbol}: " . $e->getMessage());
                return $this->getMockHistory($symbol, $from, $to);
            }
        });
    }

    private function getMockHistory($symbol, $from, $to)
    {
        $data = [];
        $startDate = \Carbon\Carbon::parse($from);
        $endDate = \Carbon\Carbon::parse($to);
        $currentPrice = $this->getMockPrice($symbol);
        
        $days = $startDate->diffInDays($endDate);
        
        for ($i = 0; $i <= $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            if ($date->isWeekend()) continue;

            $change = (rand(-200, 210) / 100);
            $currentPrice += $change;

            $data[] = [
                'date' => $date->format('Y-m-d'),
                'close' => round($currentPrice, 2),
                'volume' => rand(1000000, 5000000)
            ];
        }
        return $data;
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
                if ($response->successful()) {
                    $this->apiService->trackRequest('FMP');
                    return $response->json();
                }
                
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
                if ($response->successful()) {
                    $this->apiService->trackRequest('FMP');
                    return $response->json();
                }
                
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
                if ($response->successful()) {
                    $this->apiService->trackRequest('FMP');
                    return $response->json();
                }
                
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

    public function getProfile($symbol)
    {
        $symbol = strtoupper(trim($symbol));
        
        return Cache::remember("stock_profile_{$symbol}", 2592000, function () use ($symbol) { // 30 days
            try {
                if (empty($this->apiKey)) {
                    return $this->getMockProfile($symbol);
                }

                $url = "{$this->baseUrl}/profile/{$symbol}?apikey={$this->apiKey}";
                $response = Http::get($url);
                
                if ($response->successful() && !empty($response->json())) {
                    $this->apiService->trackRequest('FMP');
                    $profile = $response->json()[0];
                    
                    // Si es un ETF o el sector está vacío, intentar enriquecer con info sectorial
                    if (strpos(strtoupper($profile['description'] ?? ''), 'ETF') !== false || empty($profile['sector'])) {
                        $etfInfo = $this->getEtfInfo($symbol);
                        if ($etfInfo) {
                            $profile['expenseRatio'] = $etfInfo['expenseRatio'] ?? null;
                        }
                    }

                    return $profile;
                }
                
                return $this->getMockProfile($symbol);
            } catch (\Exception $e) {
                Log::error("Error fetching profile for {$symbol}: " . $e->getMessage());
                return $this->getMockProfile($symbol);
            }
        });
    }

    public function getEtfInfo($symbol)
    {
        return Cache::remember("etf_info_{$symbol}", 604800, function () use ($symbol) {
             try {
                if (empty($this->apiKey)) return ['expenseRatio' => 0.0009];
                $response = Http::get("{$this->baseUrl}/etf-info/{$symbol}?apikey={$this->apiKey}");
                if ($response->successful()) {
                    $this->apiService->trackRequest('FMP');
                    $data = $response->json();
                    return !empty($data) ? $data[0] : null;
                }
                return null;
             } catch (\Exception $e) { return null; }
        });
    }

    public function getEtfSectorWeightings($symbol)
    {
        return Cache::remember("etf_sectors_{$symbol}", 604800, function () use ($symbol) {
             try {
                if (empty($this->apiKey)) return $this->getMockSectorWeightings();
                $response = Http::get("{$this->baseUrl}/etf-sector-weightings/{$symbol}?apikey={$this->apiKey}");
                if ($response->successful()) {
                    $this->apiService->trackRequest('FMP');
                    return $response->json();
                }
                return $this->getMockSectorWeightings();
             } catch (\Exception $e) { return $this->getMockSectorWeightings(); }
        });
    }

    public function getEtfCountryWeightings($symbol)
    {
        return Cache::remember("etf_countries_{$symbol}", 604800, function () use ($symbol) {
             try {
                if (empty($this->apiKey)) return $this->getMockCountryWeightings();
                $response = Http::get("{$this->baseUrl}/etf-country-weightings/{$symbol}?apikey={$this->apiKey}");
                if ($response->successful()) {
                    $this->apiService->trackRequest('FMP');
                    return $response->json();
                }
                return $this->getMockCountryWeightings();
             } catch (\Exception $e) { return $this->getMockCountryWeightings(); }
        });
    }

    private function getMockSectorWeightings()
    {
        return [
            ['sector' => 'Technology', 'weightPercentage' => 28.5],
            ['sector' => 'Financial Services', 'weightPercentage' => 15.2],
            ['sector' => 'Healthcare', 'weightPercentage' => 12.8],
            ['sector' => 'Consumer Cyclical', 'weightPercentage' => 10.5],
            ['sector' => 'Communication Services', 'weightPercentage' => 8.4],
        ];
    }

    private function getMockCountryWeightings()
    {
        return [
            ['country' => 'United States', 'weightPercentage' => 98.5],
            ['country' => 'Other', 'weightPercentage' => 1.5],
        ];
    }

    private function getMockProfile($symbol)
    {
        $logos = [
            'AAPL' => 'https://financialmodelingprep.com/image-stock/AAPL.png',
            'MSFT' => 'https://financialmodelingprep.com/image-stock/MSFT.png',
            'GOOGL' => 'https://financialmodelingprep.com/image-stock/GOOGL.png',
            'TSLA' => 'https://financialmodelingprep.com/image-stock/TSLA.png',
            'AMZN' => 'https://financialmodelingprep.com/image-stock/AMZN.png',
            'NVDA' => 'https://financialmodelingprep.com/image-stock/NVDA.png',
            'META' => 'https://financialmodelingprep.com/image-stock/META.png',
            'SPY'  => 'https://financialmodelingprep.com/image-stock/SPY.png',
        ];

        return [
            'symbol' => $symbol,
            'companyName' => $symbol . ' Inc.',
            'image' => $logos[strtoupper($symbol)] ?? "https://ui-avatars.com/api/?name={$symbol}&background=random",
            'description' => 'Descripción simulada para el activo ' . $symbol,
            'sector' => $symbol === 'SPY' ? 'ETF' : 'Technology',
            'industry' => 'Consumer Electronics',
            'website' => 'https://example.com',
            'mktCap' => 500000000000,
            'expenseRatio' => 0.0009
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

    /**
     * Obtiene los holdings institucionales (reporte 13F más reciente).
     */
    public function getInstitutionalHoldings(string $cik)
    {
        return $this->fetchFromApi("institutional-holder/portfolio-holdings", [
            'cik' => $cik
        ]);
    }

    /**
     * Obtiene el historial de holdings institucionales (trades).
     */
    public function getInstitutionalHoldingsHistory(string $cik)
    {
        return $this->fetchFromApi("institutional-holder/portfolio-holdings-history", [
            'cik' => $cik
        ]);
    }

    /**
     * Método auxiliar para realizar peticiones a la API de FMP.
     */
    private function fetchFromApi(string $endpoint, array $params = [])
    {
        try {
            if (empty($this->apiKey)) return [];

            $queryParams = array_merge($params, ['apikey' => $this->apiKey]);
            $response = Http::get("{$this->baseUrl}/{$endpoint}", $queryParams);

            if ($response->successful()) {
                $this->apiService->trackRequest('FMP');
                return $response->json();
            }

            Log::error("FMP API error in {$endpoint}: " . $response->status());
            return [];
        } catch (\Exception $e) {
            Log::error("FMP API exception in {$endpoint}: " . $e->getMessage());
            return [];
        }
    }
}
