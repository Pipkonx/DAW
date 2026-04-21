<?php

namespace App\Services\Asset;

use App\Models\Asset;
use App\Models\MarketAsset;
use App\Services\MarketDataService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AssetService
{
    protected $marketDataService;

    public function __construct(MarketDataService $marketDataService)
    {
        $this->marketDataService = $marketDataService;
    }

    /**
     * Find an existing asset for a user by various identifiers.
     */
    public function findUserAsset($userId, $portfolioId, $identifier, $marketAssetId = null)
    {
        $query = Asset::where('user_id', $userId)
            ->where('portfolio_id', $portfolioId);

        if ($marketAssetId) {
            return $query->where('market_asset_id', $marketAssetId)->first();
        }

        return $query->where(function ($q) use ($identifier) {
            $q->where('ticker', $identifier)
              ->orWhere('isin', $identifier)
              ->orWhere('name', $identifier);
        })->first();
    }

    /**
     * Create or update an asset and ensure it is linked to market data.
     */
    public function findOrCreateAndLink($userId, array $data)
    {
        $portfolioId = $data['portfolio_id'] ?? null;
        $name = $data['name'] ?? $data['ticker'] ?? 'Unknown';
        $ticker = strtoupper($data['ticker'] ?? $name);
        $isin = $data['isin'] ?? null;
        $type = $data['type'] ?? 'stock';

        // 1. Try to find existing asset
        $asset = $this->findUserAsset($userId, $portfolioId, $ticker, $data['market_asset_id'] ?? null);

        if (!$asset) {
            $asset = Asset::create([
                'user_id' => $userId,
                'portfolio_id' => $portfolioId,
                'name' => $data['asset_full_name'] ?? $name,
                'ticker' => $ticker,
                'type' => $type,
                'market_asset_id' => $data['market_asset_id'] ?? null,
                'isin' => $isin,
                'quantity' => 0,
                'avg_buy_price' => 0,
                'current_price' => $data['price_per_unit'] ?? 0,
                'color' => $this->generateDeterministicColor($name),
            ]);
        } else {
            // Update metadata if better info provided
            $updates = [];
            if ($isin && !$asset->isin) $updates['isin'] = $isin;
            if ($type !== 'stock' && $asset->type === 'stock') $updates['type'] = $type;
            if (!empty($updates)) $asset->update($updates);
        }

        // 2. Auto-fetch price/link for new or unlinked assets
        if ($asset->wasRecentlyCreated || !$asset->market_asset_id || !$asset->current_price) {
            $this->linkToMarketData($asset, $data['asset_full_name'] ?? $name);
        }

        return $asset;
    }

    /**
     * Attempts to link an asset to a MarketAsset and fetch the latest price.
     */
    public function linkToMarketData(Asset $asset, $searchName = null)
    {
        try {
            // First try standard price fetch (which attempts auto-link internally if needed)
            $latestPrice = $this->marketDataService->getLatestPrice($asset);
            
            // If no price found, try explicit broader search
            if (!$latestPrice) {
                $nameToSearch = $searchName ?? $asset->name;
                $searchResults = $this->marketDataService->search($nameToSearch);
                
                if ($searchResults->isNotEmpty()) {
                    $bestMatch = $searchResults->first();
                    
                    $marketAsset = $this->marketDataService->syncAsset(
                        $bestMatch['ticker'], 
                        $bestMatch['type'], 
                        $bestMatch['name'], 
                        $bestMatch['currency'] ?? 'EUR', 
                        $bestMatch['isin'] ?? null
                    );
                    
                    if ($marketAsset) {
                        $asset->update([
                            'market_asset_id' => $marketAsset->id,
                            'ticker' => $marketAsset->ticker,
                            'isin' => $marketAsset->isin ?? $asset->isin,
                            'type' => $marketAsset->type
                        ]);
                        
                        $latestPrice = $this->marketDataService->getLatestPrice($asset);
                    }
                }
            }

            if ($latestPrice) {
                $asset->update(['current_price' => $latestPrice]);
            }

            return $latestPrice;
        } catch (\Exception $e) {
            Log::warning("AssetService: Failed to link/fetch price for {$asset->name}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper to generate a deterministic dark color for UI display.
     */
    public function generateDeterministicColor($str)
    {
        $hash = md5($str);
        $r = hexdec(substr($hash, 0, 2)) % 128; 
        $g = hexdec(substr($hash, 2, 2)) % 128;
        $b = hexdec(substr($hash, 4, 2)) % 128;
        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
}
