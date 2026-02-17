<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MarketData\FundService;
use App\Services\MarketDataService;

class TestFundPrice extends Command
{
    protected $signature = 'test:fund-price';
    protected $description = 'Test fund price lookup for specific problematic funds';

    public function handle(FundService $fundService, MarketDataService $marketDataService)
    {
        $funds = [
            'ISHARES DEVELOPED WORLD INDEX (IE) ACC EUR CLASE S',
            'ISHARES EMERGING MARKETS INDEX FUND (IE) ACC EUR CLASE S',
            'VANGUARD GLOBAL SMALL-CAP INDEX FUND EUR ACC' // Should work
        ];

        foreach ($funds as $fundName) {
            $this->info("Testing: $fundName");
            
            // 1. Search by Name via FundService
            $this->info("  Searching via FundService...");
            $isin = $fundService->searchByName($fundName);
            
            if ($isin) {
                if (is_array($isin)) {
                    $this->info("  Found Match: " . json_encode($isin));
                    $isinCode = $isin['isin'];
                } else {
                    $this->info("  Found ISIN: $isin");
                    $isinCode = $isin;
                }
                
                // 2. Get Price via FundService
                $priceData = $fundService->getPrice($isinCode);
                if (is_array($priceData)) {
                    $this->info("  Price: " . $priceData['price'] . " (Date: " . $priceData['date'] . ")");
                } else {
                    $this->info("  Price: " . ($priceData ?? 'NULL'));
                }
            } else {
                $this->error("  ISIN not found via FundService");
                
                // Try with MarketDataService searchAndLinkByName logic
                // This logic is private inside MarketDataService, so we can't call it directly easily.
                // But we can simulate what getLatestPrice does if ISIN is missing.
                // Actually MarketDataService::getLatestPrice relies on ISIN or ticker.
                // If ISIN is missing, it returns null unless we implemented a fallback.
            }
            
            $this->newLine();
        }
    }
}
