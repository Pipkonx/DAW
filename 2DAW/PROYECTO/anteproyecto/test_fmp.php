<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

$apiKey = config('services.fmp.key');
$symbol = 'AAPL';
$url = "https://financialmodelingprep.com/api/v3/quote/{$symbol}?apikey={$apiKey}";

echo "Testing URL: $url\n";
$response = Http::get($url);

echo "Status: " . $response->status() . "\n";
echo "Body: " . $response->body() . "\n";

$url2 = "https://financialmodelingprep.com/api/v3/quote-short/{$symbol}?apikey={$apiKey}";
echo "\nTesting URL 2 (quote-short): $url2\n";
$response2 = Http::get($url2);

echo "Status: " . $response2->status() . "\n";
echo "Body: " . $response2->body() . "\n";
