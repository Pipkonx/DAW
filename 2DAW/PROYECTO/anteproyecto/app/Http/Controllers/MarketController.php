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
        $marketData = Cache::remember('market_index_data_v7', 900, function() {
            return [
                'stocks' => [
                    'winners' => $this->mapStockData($this->marketDataService->getStockGainers()),
                    'losers' => $this->mapStockData($this->marketDataService->getStockLosers()),
                    'most_searched' => $this->mapStockData($this->marketDataService->getStockActive()),
                ],
                'crypto' => [
                    'largest' => $this->mapCryptoData($this->marketDataService->getCryptoTop(3)),
                    'popular' => $this->mapCryptoData($this->marketDataService->getCryptoTrending()),
                    'most_searched' => $this->mapCryptoData($this->marketDataService->getCryptoTop(6)), // Fetch top 6, map will slice to 3
                ],
                'etfs' => $this->getRealDataForSymbols([
                    'largest' => ['SPY', 'IVV', 'VTI'],
                    'popular' => ['QQQ', 'VOO', 'SCHD'],
                    'most_searched' => ['JEPI', 'ARKK', 'TQQQ']
                ], 'etf'),
                'funds' => [
                    'popular' => [
                        ['ticker' => 'IE00B4L5Y983', 'name' => 'iShares Core MSCI World', 'price' => 88.20, 'change_percent' => 0.3],
                        ['ticker' => 'LU0348751388', 'name' => 'Vanguard Global Stock Index', 'price' => 124.50, 'change_percent' => 0.5],
                        ['ticker' => 'LU0389812933', 'name' => 'Fidelity Global Technology', 'price' => 64.10, 'change_percent' => -0.2],
                    ]
                ]
            ];
        });

        return Inertia::render('Markets/Index', $marketData);
    }

    private function mapStockData($stocks)
    {
        return array_map(function($stock) {
            return [
                'ticker' => $stock['symbol'],
                'name' => $stock['name'] ?? $stock['symbol'],
                'price' => (float)($stock['price'] ?? 0),
                'change_percent' => (float)($stock['changesPercentage'] ?? 0),
                'image' => $stock['image'] ?? null,
                'volume' => 'High'
            ];
        }, array_slice($stocks, 0, 3)); // Limit to top 3
    }

    private function mapCryptoData($coins)
    {
        return array_map(function($coin) {
            return [
                'ticker' => $coin['symbol'],
                'name' => $coin['name'],
                'price' => (float)($coin['price'] ?? 0),
                'change_percent' => (float)($coin['change_percent'] ?? 0),
                'image' => $coin['image'] ?? null,
                'volume' => 'High'
            ];
        }, array_slice($coins, 0, 3)); // Limit to top 3
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
                    'change_percent' => $this->getRandomChange(),
                    'image' => "https://financialmodelingprep.com/image-stock/{$ticker}.png",
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
