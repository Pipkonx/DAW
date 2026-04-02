<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\MarketDataService;
use App\Models\MarketAsset;

$service = app(MarketDataService::class);

echo "Testing BTC price fetch...\n";
// Create or get a MarketAsset for BTC without api_id
$btc = MarketAsset::updateOrCreate(
    ['ticker' => 'BTC', 'type' => 'crypto'],
    ['name' => 'Bitcoin', 'api_id' => null]
);

$price = $service->getLatestPrice($btc);
echo "Result BTC: " . ($price ?: 'NULL') . "\n";
echo "Updated API ID BTC: " . ($btc->fresh()->api_id ?: 'NULL') . "\n\n";

echo "Testing ETH (only symbol)...\n";
$eth = MarketAsset::updateOrCreate(
    ['ticker' => 'ETH', 'type' => 'crypto'],
    ['name' => 'ETH', 'api_id' => null]
);
$priceEth = $service->getLatestPrice($eth);
echo "Result ETH: " . ($priceEth ?: 'NULL') . "\n";
echo "Updated API ID ETH: " . ($eth->fresh()->api_id ?: 'NULL') . "\n\n";

echo "Testing SPY (Stock)...\n";
$spy = MarketAsset::updateOrCreate(
    ['ticker' => 'SPY', 'type' => 'stock'],
    ['name' => 'SPDR S&P 500 ETF Trust']
);
$priceSpy = $service->getLatestPrice($spy);
echo "Result SPY: " . ($priceSpy ?: 'NULL') . "\n";
