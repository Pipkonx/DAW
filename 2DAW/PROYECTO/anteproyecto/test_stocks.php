<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\MarketDataService;
use App\Models\MarketAsset;
use Illuminate\Support\Facades\Log;

$service = app(MarketDataService::class);

echo "Testing SPY (Stock/ETF)...\n";
$spy = MarketAsset::where('ticker', 'SPY')->where('type', 'stock')->first() ?: MarketAsset::create([
    'ticker' => 'SPY',
    'name' => 'SPDR S&P 500 ETF Trust',
    'type' => 'stock'
]);

$priceSpy = $service->getLatestPrice($spy);
echo "Result SPY: " . ($priceSpy ?: 'NULL') . "\n";

echo "Testing NVDA (Stock)...\n";
$nvda = MarketAsset::where('ticker', 'NVDA')->where('type', 'stock')->first() ?: MarketAsset::create([
    'ticker' => 'NVDA',
    'name' => 'NVIDIA Corporation',
    'type' => 'stock'
]);
$priceNvda = $service->getLatestPrice($nvda);
echo "Result NVDA: " . ($priceNvda ?: 'NULL') . "\n";
