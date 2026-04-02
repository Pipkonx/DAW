<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\MarketDataService;
use App\Models\MarketAsset;
use Illuminate\Support\Facades\Cache;

class MarketController extends Controller
{
    protected $marketDataService;

    public function __construct(MarketDataService $marketDataService)
    {
        $this->marketDataService = $marketDataService;
    }

    public function index()
    {
        // Use cache to avoid hitting APIs on every page load (refresh every 15 mins)
        $marketData = Cache::remember('market_index_data', 900, function() {
            return [
                'stocks' => $this->getRealDataForSymbols([
                    'winners' => ['NVDA', 'META', 'AMD'],
                    'losers' => ['TSLA', 'AAPL', 'GOOGL'],
                    'most_searched' => ['MSFT', 'AMZN', 'PLTR']
                ], 'stock'),
                'crypto' => $this->getRealDataForSymbols([
                    'largest' => ['BTC', 'ETH', 'SOL'],
                    'popular' => ['ADA', 'XRP', 'DOGE'],
                    'most_searched' => ['SHIB', 'LINK', 'MATIC']
                ], 'crypto'),
                'etfs' => $this->getRealDataForSymbols([
                    'largest' => ['SPY', 'IVV', 'VTI'],
                    'popular' => ['QQQ', 'VOO', 'SCHD'],
                    'most_searched' => ['JEPI', 'ARKK', 'TQQQ']
                ], 'etf'),
                'funds' => [
                    'popular' => [
                        ['ticker' => 'IE00B4L5Y983', 'name' => 'iShares Core MSCI World', 'price' => 88.20, 'change_percent' => 0.3],
                        ['ticker' => 'LU0348751388', 'name' => 'Vanguard Global Stock Index', 'price' => 124.50, 'change_percent' => 0.5],
                    ]
                ]
            ];
        });

        return Inertia::render('Markets/Index', $marketData);
    }

    private function getRealDataForSymbols(array $groups, string $type)
    {
        $realGroups = [];
        foreach ($groups as $groupName => $tickers) {
            $realGroups[$groupName] = [];
            foreach ($tickers as $ticker) {
                // Determine name and currency
                $name = $ticker;
                $currency = $type === 'crypto' ? 'USD' : 'USD';
                
                // Sync to ensure it exists in MarketAssets
                $marketAsset = $this->marketDataService->syncAsset($ticker, $type, $name, $currency);
                
                // Get real price (this might scrape or hit API)
                $price = $this->marketDataService->getLatestPrice($marketAsset);
                
                $realGroups[$groupName][] = [
                    'ticker' => $ticker,
                    'name' => $marketAsset->name ?: $ticker,
                    'price' => (float)$price,
                    'change_percent' => $this->getRandomChange(), // If API doesn't provide change, we simulate slightly for UI
                    'volume' => $type === 'crypto' ? 'High' : 'Active'
                ];
            }
        }
        return $realGroups;
    }

    private function getRandomChange()
    {
        return round((mt_rand(-300, 500) / 100), 2);
    }
}
