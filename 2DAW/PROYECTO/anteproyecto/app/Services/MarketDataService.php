<?php

namespace App\Services;

use App\Services\MarketData\StockService;
use App\Services\MarketData\CryptoService;
use App\Services\MarketData\BondService;
use App\Models\Asset;
use App\Models\AssetPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

use App\Models\MarketAsset;

class MarketDataService
{
    protected $stockService;
    protected $cryptoService;
    protected $bondService;

    public function __construct(
        StockService $stockService,
        CryptoService $cryptoService,
        BondService $bondService
    ) {
        $this->stockService = $stockService;
        $this->cryptoService = $cryptoService;
        $this->bondService = $bondService;
    }

    public function getLatestPrice($assetOrMarketAsset)
    {
        $marketAsset = null;
        $asset = null;

        if ($assetOrMarketAsset instanceof Asset) {
            $asset = $assetOrMarketAsset;
            $marketAsset = $asset->marketAsset;
        } elseif ($assetOrMarketAsset instanceof MarketAsset) {
            $marketAsset = $assetOrMarketAsset;
        }

        if (!$marketAsset) {
            // If we only have an Asset without a linked MarketAsset, we can try to fetch by ticker
            // but for now let's assume we need a market asset or at least a ticker.
            if ($asset) {
                 return $this->fetchPriceByTicker($asset->ticker, $asset->type);
            }
            return null;
        }

        // 1. Check if we have a recent price in our database (e.g. from today)
        $today = Carbon::today();
        $latestPrice = AssetPrice::where('market_asset_id', $marketAsset->id)
            ->where('date', $today)
            ->first();

        if ($latestPrice) {
            return $latestPrice->price;
        }

        // 2. If not, fetch from API
        $price = $this->fetchPriceFromApi($marketAsset);

        // 3. Store the result in DB if valid
        if ($price) {
            AssetPrice::updateOrCreate(
                [
                    'market_asset_id' => $marketAsset->id,
                    'date' => $today,
                ],
                [
                    'price' => $price,
                    'source' => 'api',
                    'volume' => 0 
                ]
            );
            
            // Also update the market asset's current price
            $marketAsset->update(['current_price' => $price]);
            
            // And the user asset if provided
            if ($asset) {
                $asset->update(['current_price' => $price]);
            }
        }

        return $price ?? ($asset ? $asset->current_price : $marketAsset->current_price);
    }

    public function getHistoricalPrice($marketAssetOrId, $date)
    {
        $marketAsset = $marketAssetOrId instanceof MarketAsset 
            ? $marketAssetOrId 
            : MarketAsset::find($marketAssetOrId);

        if (!$marketAsset) {
            return null;
        }

        // Check DB first
        $existingPrice = AssetPrice::where('market_asset_id', $marketAsset->id)
            ->where('date', $date)
            ->first();

        if ($existingPrice) {
            return $existingPrice->price;
        }

        // Fetch from API
        $price = $this->fetchHistoricalPriceFromApi($marketAsset, $date);

        if ($price) {
            AssetPrice::updateOrCreate(
                [
                    'market_asset_id' => $marketAsset->id,
                    'date' => $date,
                ],
                [
                    'price' => $price,
                    'source' => 'api',
                ]
            );
        }

        return $price;
    }

    public function syncAsset($ticker, $type, $name, $currency = 'USD')
    {
        // Try to find by ticker and type
        $marketAsset = MarketAsset::where('ticker', $ticker)
            ->where('type', $type)
            ->first();

        if (!$marketAsset) {
            $marketAsset = MarketAsset::create([
                'ticker' => strtoupper($ticker),
                'name' => $name,
                'type' => $type,
                'currency_code' => $currency,
            ]);
        }
        
        return $marketAsset;
    }

    public function search($query)
    {
        // 1. Local Search
        $localResults = MarketAsset::where('ticker', 'LIKE', "%{$query}%")
            ->orWhere('name', 'LIKE', "%{$query}%")
            ->orWhere('isin', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();

        // Map local results to standard format
        $results = $localResults->map(function ($asset) {
            return [
                'id' => $asset->id,
                'ticker' => $asset->ticker,
                'name' => $asset->name,
                'type' => $asset->type,
                'currency' => $asset->currency_code,
                'source' => 'local',
                'isin' => $asset->isin,
            ];
        });

        // 2. API Search (if needed or to supplement)
        // Only if query is long enough to be specific
        if (strlen($query) >= 3) {
            try {
                $apiResults = $this->searchAssets($query);
                
                foreach ($apiResults as $apiItem) {
                    // Check if already in local results by ticker
                    if (!$results->contains('ticker', $apiItem['symbol'])) {
                        $results->push([
                            'id' => null, // Not in DB yet
                            'ticker' => $apiItem['symbol'],
                            'name' => $apiItem['name'],
                            'type' => $apiItem['type'],
                            'currency' => $apiItem['currency'] ?? 'USD',
                            'source' => 'api',
                            'api_id' => $apiItem['api_id'] ?? null,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error("MarketDataService API Search error: " . $e->getMessage());
            }
        }

        return $results;
    }

    private function fetchPriceFromApi(MarketAsset $marketAsset)
    {
        try {
            switch ($marketAsset->type) {
                case 'crypto':
                    // Ideally use a dedicated API ID field, fallback to name/ticker
                    $identifier = strtolower($marketAsset->name); // CoinGecko uses names like 'bitcoin'
                    return $this->cryptoService->getPrice($identifier);
                case 'bond':
                    $identifier = $marketAsset->isin ?? $marketAsset->ticker;
                    return $this->bondService->getPrice($identifier);
                case 'stock':
                case 'etf':
                default:
                    return $this->stockService->getPrice($marketAsset->ticker);
            }
        } catch (\Exception $e) {
            Log::error("MarketDataService API error for {$marketAsset->ticker}: " . $e->getMessage());
            return null;
        }
    }

    private function fetchHistoricalPriceFromApi(MarketAsset $marketAsset, $date)
    {
        try {
            switch ($marketAsset->type) {
                case 'crypto':
                    $identifier = strtolower($marketAsset->name);
                    return $this->cryptoService->getHistoricalPrice($identifier, $date);
                case 'bond':
                    $identifier = $marketAsset->isin ?? $marketAsset->ticker;
                    return $this->bondService->getHistoricalPrice($identifier, $date);
                case 'stock':
                case 'etf':
                default:
                    return $this->stockService->getHistoricalPrice($marketAsset->ticker, $date);
            }
        } catch (\Exception $e) {
            Log::error("MarketDataService Historical API error for {$marketAsset->ticker}: " . $e->getMessage());
            return null;
        }
    }
    
    // Helper fallback for assets without market_asset link (legacy/manual)
    private function fetchPriceByTicker($ticker, $type)
    {
         // Implementation similar to above but without MarketAsset model
         // For brevity, skipping for now as we are migrating to MarketAsset
         return null;
    }

    private function searchAssets($query)
    {
        $results = [];

        // Search Stocks (FMP)
        try {
            $stocks = $this->stockService->search($query);
            foreach ($stocks as $stock) {
                $results[] = [
                    'symbol' => $stock['symbol'],
                    'name' => $stock['name'],
                    'type' => 'stock',
                    'currency' => $stock['currency'],
                    'exchange' => $stock['stockExchange'],
                ];
            }
        } catch (\Exception $e) {
            Log::error("Stock Search Error: " . $e->getMessage());
        }

        // Search Crypto (CoinGecko)
        try {
            $cryptos = $this->cryptoService->search($query);
            foreach ($cryptos as $crypto) {
                $results[] = [
                    'symbol' => strtoupper($crypto['symbol']),
                    'name' => $crypto['name'],
                    'type' => 'crypto',
                    'currency' => 'USD',
                    'api_id' => $crypto['id'],
                ];
            }
        } catch (\Exception $e) {
            Log::error("Crypto Search Error: " . $e->getMessage());
        }

        return $results;
    }
}
