<?php

namespace App\Services;

use App\Services\MarketData\StockService;
use App\Services\MarketData\CryptoService;
use App\Services\MarketData\BondService;
use App\Services\MarketData\FundService;
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
    protected $fundService;

    public function __construct(
        StockService $stockService,
        CryptoService $cryptoService,
        BondService $bondService,
        FundService $fundService
    ) {
        $this->stockService = $stockService;
        $this->cryptoService = $cryptoService;
        $this->bondService = $bondService;
        $this->fundService = $fundService;
    }

    /**
     * Attempts to find a market asset by name if ticker/ISIN are missing.
     */
    private function searchAndLinkByName(Asset $asset)
    {
        if (!$asset->name) return null;

        // Try FundService search (uses FT which covers Funds, ETFs, and Stocks)
        $result = $this->fundService->searchByName($asset->name);

        if ($result && isset($result['isin'])) {
            $isin = $result['isin'];
            $name = $result['name'];
            
            // Use ISIN as ticker (often it's the ticker symbol for stocks/US funds)
            $ticker = $isin; 
            
            $marketAsset = $this->syncAsset($ticker, $asset->type, $name, $asset->currency_code ?? 'EUR', $isin);
            
            if ($marketAsset) {
                $asset->update([
                    'market_asset_id' => $marketAsset->id,
                    'isin' => $isin,
                    'ticker' => $ticker
                ]);
                return $marketAsset;
            }
        }
        
        return null;
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
            // If we only have an Asset without a linked MarketAsset, try to auto-link
            if ($asset) {
                if ($asset->ticker) {
                    $currency = $asset->currency_code ?? 'USD';
                    $isin = $asset->isin ?? null;
                    $marketAsset = $this->syncAsset($asset->ticker, $asset->type, $asset->name, $currency, $isin);
                }

                // If sync failed or we have no ticker, try search by name
                if (!$marketAsset || !$marketAsset->ticker) {
                    $foundAsset = $this->searchAndLinkByName($asset);
                    if ($foundAsset) {
                        $marketAsset = $foundAsset;
                    }
                }
                
                if ($marketAsset) {
                    $asset->update(['market_asset_id' => $marketAsset->id]);
                } else {
                    return null;
                }
            } else {
                return null;
            }
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
        $apiResult = $this->fetchPriceFromApi($marketAsset);
        
        $price = null;
        $date = $today;

        if (is_array($apiResult) && isset($apiResult['price'])) {
            $price = $apiResult['price'];
            if (isset($apiResult['date'])) {
                $date = $apiResult['date'];
            }
        } elseif (is_numeric($apiResult)) {
            $price = $apiResult;
        }

        // 3. Store the result in DB if valid
        if ($price) {
            AssetPrice::updateOrCreate(
                [
                    'market_asset_id' => $marketAsset->id,
                    'date' => $date,
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
        // For funds, we might not have historical data via scraping easily
        // But let's keep the structure
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

    public function syncAsset($ticker, $type, $name, $currency = 'USD', $isin = null)
    {
        // Try to find by ticker and type (and isin if provided)
        $query = MarketAsset::where('type', $type);
        
        if ($isin) {
             $query->where(function($q) use ($ticker, $isin) {
                 $q->where('ticker', $ticker)->orWhere('isin', $isin);
             });
        } else {
             $query->where('ticker', $ticker);
        }
        
        $marketAsset = $query->first();

        if (!$marketAsset) {
            $marketAsset = MarketAsset::create([
                'ticker' => strtoupper($ticker),
                'name' => $name,
                'type' => $type,
                'currency_code' => $currency,
                'isin' => $isin,
            ]);
        } elseif ($isin && !$marketAsset->isin) {
             // Update ISIN if we found it by ticker but ISIN was missing
             $marketAsset->update(['isin' => $isin]);
        }
        
        return $marketAsset;
    }

    public function findOrLinkAsset($name, $typeHint = 'fund')
    {
        // 1. Try to find locally by ISIN (if name looks like ISIN)
        if (preg_match('/^[A-Z]{2}[A-Z0-9]{9}\d$/', $name)) {
            $local = MarketAsset::where('isin', $name)->first();
            if ($local) return $local;
            
            // If not found locally but is ISIN, create it as fund
            return MarketAsset::create([
                'ticker' => $name,
                'name' => $name,
                'type' => $typeHint,
                'currency_code' => 'EUR', // Default
                'isin' => $name,
            ]);
        }

        // 2. Try to find locally by name
        $local = MarketAsset::where('name', 'LIKE', "%{$name}%")
            ->where('type', $typeHint)
            ->first();
        if ($local) return $local;

        // 3. If fund, try scraping search
        if ($typeHint === 'fund') {
            $searchResult = $this->fundService->searchByName($name);
            if ($searchResult) {
                // Check if already exists by ISIN found
                $existing = MarketAsset::where('isin', $searchResult['isin'])->first();
                if ($existing) return $existing;

                // Create new MarketAsset from search result
                return MarketAsset::create([
                    'ticker' => $searchResult['isin'], // Use ISIN as ticker for funds if no ticker
                    'name' => $searchResult['name'],
                    'type' => 'fund',
                    'currency_code' => 'EUR', // Usually EUR in Morningstar.es
                    'isin' => $searchResult['isin'],
                ]);
            }
        }

        return null;
    }

    public function search($query, $type = null)
    {
        // 1. Local Search
        $localQuery = MarketAsset::where(function ($q) use ($query) {
            $q->where('ticker', 'LIKE', "%{$query}%")
              ->orWhere('name', 'LIKE', "%{$query}%")
              ->orWhere('isin', 'LIKE', "%{$query}%");
        });
        
        if ($type) {
            $localQuery->where('type', $type);
        }

        $localResults = $localQuery->limit(10)->get();

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
                // If query looks like ISIN or user might be searching for fund
                if ($type === 'fund' || preg_match('/^[A-Z]{2}[A-Z0-9]{9}\d$/', $query) || strlen($query) > 5) {
                    if (!$type || $type === 'fund' || $type === 'etf') {
                        $fundResult = $this->fundService->searchByName($query);
                        if ($fundResult) {
                            // Check if already in results
                            if (!$results->contains('isin', $fundResult['isin'])) {
                                $results->push([
                                    'id' => null,
                                    'ticker' => $fundResult['isin'], // Funds often use ISIN as ticker
                                    'name' => $fundResult['name'],
                                    'type' => 'fund',
                                    'currency_code' => 'EUR', // Default for Morningstar.es
                                    'source' => 'api',
                                    'isin' => $fundResult['isin'],
                                ]);
                            }
                        }
                    }
                }

                $apiResults = $this->searchAssets($query, $type);
                
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
                case 'fund':
                    // Use scraping service for funds (ISIN required)
                    if (!$marketAsset->isin) {
                        // Try to find ISIN by name
                        $found = $this->fundService->searchByName($marketAsset->name);
                        if ($found && isset($found['isin'])) {
                            $marketAsset->update(['isin' => $found['isin']]);
                            // Retry with new ISIN
                            return $this->fundService->getPrice($found['isin']);
                        }
                        return null;
                    }
                    return $this->fundService->getPrice($marketAsset->isin);
                case 'stock':
                case 'etf':
                default:
                    $price = $this->stockService->getPrice($marketAsset->ticker);
                    
                    if (!$price && ($marketAsset->type === 'stock' || $marketAsset->type === 'other')) {
                         // Fallback: Check if it's actually a fund by name
                         // This handles cases where user added a fund as a stock or other
                         $fundCheck = $this->fundService->searchByName($marketAsset->name);
                         if ($fundCheck && isset($fundCheck['isin'])) {
                             // It's actually a fund! Update type and ISIN.
                             $marketAsset->update([
                                 'type' => 'fund', 
                                 'isin' => $fundCheck['isin'],
                                 'ticker' => $fundCheck['isin'] // Update ticker to ISIN for consistency
                             ]);
                             return $this->fundService->getPrice($fundCheck['isin']);
                         }
                    }
                    return $price;
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
                case 'fund':
                    // Historical scraping not supported yet. 
                    // Fallback to latest price if date is recent (within 7 days)
                    if (Carbon::parse($date)->diffInDays(now()) <= 7) {
                         return $this->fundService->getPrice($marketAsset->isin);
                    }
                    return null;
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

    private function searchAssets($query, $type = null)
    {
        $results = [];

        // Search Stocks (FMP)
        if (!$type || $type === 'stock' || $type === 'etf') {
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
        }

        // Search Crypto (CoinGecko)
        if (!$type || $type === 'crypto') {
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
        }

        return $results;
    }
}
