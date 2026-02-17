<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdatePricesJob;
use App\Services\MarketDataService;

class UpdatePricesNow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prices:update-now';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger the price update job immediately';

    /**
     * Execute the console command.
     */
    public function handle(MarketDataService $marketData)
    {
        $this->info('Starting price update job...');
        
        $job = new UpdatePricesJob();
        $job->handle($marketData);
        
        $this->info('Price update job finished.');
    }
}
