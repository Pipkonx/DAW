<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Asset;
use App\Models\MarketAsset;
use App\Services\MarketDataService;
use Illuminate\Support\Facades\Log;

class UpdatePricesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(MarketDataService $marketData): void
    {
        // 1. Get all unique market assets used by users
        // Use pluck on MarketAsset directly might be better if we want to update all market assets, 
        // but updating only used assets saves API calls.
        $marketAssetIds = Asset::whereNotNull('market_asset_id')
            ->distinct()
            ->pluck('market_asset_id');

        Log::info("Starting UpdatePricesJob for " . count($marketAssetIds) . " market assets.");

        foreach ($marketAssetIds as $id) {
            $marketAsset = MarketAsset::find($id);
            if (!$marketAsset) continue;

            try {
                $asset = Asset::where('market_asset_id', $id)->first();
                if ($asset) {
                    $price = $marketData->getLatestPrice($asset);
                    Log::info("Updated price for {$marketAsset->ticker}: {$price}");
                }
            } catch (\Exception $e) {
                Log::error("Failed to update price for {$marketAsset->ticker}: " . $e->getMessage());
            }
            
            // Optional: Add small delay if processing many to be gentle on API
            usleep(200000); // 0.2s
        }

        // 2. Handle assets WITHOUT market_asset_id (Added by name)
        // These are the ones the user complained about
        $unlinkedAssets = Asset::whereNull('market_asset_id')
            ->whereNotNull('name')
            ->get();

        Log::info("Checking " . count($unlinkedAssets) . " unlinked assets for price updates.");

        foreach ($unlinkedAssets as $asset) {
            try {
                // This will trigger searchAndLinkByName internally if possible
                $price = $marketData->getLatestPrice($asset);
                
                if ($price) {
                    Log::info("Updated price and linked asset: {$asset->name} -> {$price}");
                } else {
                    Log::warning("Could not find price for unlinked asset: {$asset->name}");
                }
            } catch (\Exception $e) {
                Log::error("Failed to update price for unlinked asset {$asset->name}: " . $e->getMessage());
            }
            
            usleep(500000); // 0.5s delay for scraping safety
        }
        
        Log::info("UpdatePricesJob completed.");
    }
}
