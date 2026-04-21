<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Asset;
use App\Services\MarketDataService;
use App\Models\User;

class TestPriceLookup extends Command
{
    protected $signature = 'test:price-lookup {name} {--ticker=} {--type=fund}';
    protected $description = 'Test price lookup for asset by name';

    public function handle(MarketDataService $marketDataService)
    {
        $name = $this->argument('name');
        $ticker = $this->option('ticker');
        $type = $this->option('type');

        $this->info("Testing price lookup for: $name (Type: $type, Ticker: " . ($ticker ?? 'NULL') . ")");

        // Create a dummy user if needed or use existing
        $user = User::first() ?? User::factory()->create();

        // Create a dummy asset with ONLY name
        // Use a unique name to avoid conflicts with existing assets
        $asset = Asset::create([
            'user_id' => $user->id,
            'name' => $name,
            'ticker' => $ticker,
            'type' => $type,
            'quantity' => 1,
            'avg_buy_price' => 100,
        ]);

        $this->info("Asset created with ID: {$asset->id}");
        $this->info("Initial Market Asset ID: " . ($asset->market_asset_id ?? 'NULL'));
        $this->info("Initial ISIN: " . ($asset->isin ?? 'NULL'));

        // Try to get price
        $this->info("Fetching price...");
        $price = $marketDataService->getLatestPrice($asset);

        $this->info("Price returned: " . ($price ?? 'NULL'));

        // Reload asset
        $asset->refresh();

        $this->info("Final Market Asset ID: " . ($asset->market_asset_id ?? 'NULL'));
        $this->info("Final ISIN: " . ($asset->isin ?? 'NULL'));
        $this->info("Final Ticker: " . $asset->ticker);

        if ($price && $asset->market_asset_id) {
            $this->info("SUCCESS: Price found and asset linked.");
        } else {
            $this->error("FAILURE: Price not found or asset not linked.");
        }

        // Cleanup
        $asset->delete();
        if ($asset->marketAsset) {
             // Optional: delete created market asset if you want to keep DB clean
             // $asset->marketAsset->delete();
        }
    }
}
