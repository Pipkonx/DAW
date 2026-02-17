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

        Log::info("Starting UpdatePricesJob for " . count($marketAssetIds) . " assets.");

        foreach ($marketAssetIds as $id) {
            $marketAsset = MarketAsset::find($id);
            if (!$marketAsset) continue;

            try {
                // Fetch current price (Service handles caching and DB storage)
                // We create a temporary Asset object or pass the MarketAsset if the service supports it.
                // My MarketDataService::getLatestPrice expects an Asset model.
                // I should update MarketDataService to accept MarketAsset too, or just use one of the assets.
                
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
        
        Log::info("UpdatePricesJob completed.");
    }
}
