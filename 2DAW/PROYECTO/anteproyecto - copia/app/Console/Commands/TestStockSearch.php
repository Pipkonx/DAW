<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MarketData\StockService;

class TestStockSearch extends Command
{
    protected $signature = 'test:stock-search {query}';
    protected $description = 'Test stock search';

    public function handle(StockService $stockService)
    {
        $query = $this->argument('query');
        $this->info("Searching for: $query");

        $results = $stockService->search($query);
        
        $this->info("Results found: " . count($results));
        foreach ($results as $result) {
            $this->info(json_encode($result));
        }
    }
}
