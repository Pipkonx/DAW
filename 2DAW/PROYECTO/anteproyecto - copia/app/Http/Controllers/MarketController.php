<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\MarketDataService;

class MarketController extends Controller
{
    protected $marketDataService;

    public function __construct(MarketDataService $marketDataService)
    {
        $this->marketDataService = $marketDataService;
    }

    public function index()
    {
        // En una implementación real, estos datos vendrían de una API externa (FMP, CoinGecko, etc.)
        // Como no tenemos endpoints de "Trending" configurados en el servicio aún,
        // usaremos datos de ejemplo estructurados para la vista.
        
        return Inertia::render('Markets/Index', [
            'stocks' => [
                'winners' => [
                    ['ticker' => 'NVDA', 'name' => 'NVIDIA Corp', 'price' => 726.13, 'change' => 2.45, 'change_percent' => 3.5, 'volume' => '45.2M'],
                    ['ticker' => 'META', 'name' => 'Meta Platforms', 'price' => 473.32, 'change' => 12.40, 'change_percent' => 2.1, 'volume' => '18.5M'],
                    ['ticker' => 'AMZN', 'name' => 'Amazon.com', 'price' => 174.45, 'change' => 2.15, 'change_percent' => 1.2, 'volume' => '32.1M'],
                ],
                'losers' => [
                    ['ticker' => 'TSLA', 'name' => 'Tesla Inc', 'price' => 193.57, 'change' => -4.20, 'change_percent' => -2.1, 'volume' => '89.4M'],
                    ['ticker' => 'AAPL', 'name' => 'Apple Inc', 'price' => 182.31, 'change' => -1.10, 'change_percent' => -0.6, 'volume' => '50.2M'],
                    ['ticker' => 'GOOGL', 'name' => 'Alphabet Inc', 'price' => 141.76, 'change' => -0.85, 'change_percent' => -0.5, 'volume' => '22.8M'],
                ],
                'most_searched' => [
                    ['ticker' => 'MSFT', 'name' => 'Microsoft Corp', 'price' => 420.55, 'change_percent' => 0.8, 'volume' => '20.1M'],
                    ['ticker' => 'AMD', 'name' => 'Advanced Micro Devices', 'price' => 176.23, 'change_percent' => 1.5, 'volume' => '65.3M'],
                    ['ticker' => 'PLTR', 'name' => 'Palantir Technologies', 'price' => 24.50, 'change_percent' => 4.2, 'volume' => '110.5M'],
                ]
            ],
            'etfs' => [
                'largest' => [
                    ['ticker' => 'SPY', 'name' => 'SPDR S&P 500 ETF', 'price' => 498.84, 'change_percent' => 0.4, 'volume' => '75.2M'],
                    ['ticker' => 'IVV', 'name' => 'iShares Core S&P 500', 'price' => 500.12, 'change_percent' => 0.4, 'volume' => '4.5M'],
                    ['ticker' => 'VTI', 'name' => 'Vanguard Total Stock Market', 'price' => 245.67, 'change_percent' => 0.3, 'volume' => '3.2M'],
                ],
                'popular' => [
                    ['ticker' => 'QQQ', 'name' => 'Invesco QQQ Trust', 'price' => 432.11, 'change_percent' => 0.6, 'volume' => '35.8M'],
                    ['ticker' => 'VCOO', 'name' => 'Vanguard S&P 500', 'price' => 460.20, 'change_percent' => 0.4, 'volume' => '2.1M'],
                    ['ticker' => 'SCHD', 'name' => 'Schwab US Dividend Equity', 'price' => 77.45, 'change_percent' => -0.1, 'volume' => '1.8M'],
                ],
                'most_searched' => [
                    ['ticker' => 'JEPI', 'name' => 'JPMorgan Equity Premium', 'price' => 56.78, 'change_percent' => 0.1, 'volume' => '4.2M'],
                    ['ticker' => 'ARKK', 'name' => 'ARK Innovation ETF', 'price' => 48.90, 'change_percent' => -1.2, 'volume' => '12.5M'],
                    ['ticker' => 'TQQQ', 'name' => 'ProShares UltraPro QQQ', 'price' => 58.23, 'change_percent' => 1.8, 'volume' => '65.4M'],
                ]
            ],
            'crypto' => [
                'largest' => [
                    ['ticker' => 'BTC', 'name' => 'Bitcoin', 'price' => 52145.20, 'change_percent' => 1.2, 'volume' => '$25.4B'],
                    ['ticker' => 'ETH', 'name' => 'Ethereum', 'price' => 2845.10, 'change_percent' => 2.5, 'volume' => '$12.1B'],
                    ['ticker' => 'SOL', 'name' => 'Solana', 'price' => 112.40, 'change_percent' => 4.1, 'volume' => '$3.2B'],
                ],
                'popular' => [
                    ['ticker' => 'ADA', 'name' => 'Cardano', 'price' => 0.58, 'change_percent' => -0.5, 'volume' => '$450M'],
                    ['ticker' => 'XRP', 'name' => 'XRP', 'price' => 0.55, 'change_percent' => 0.2, 'volume' => '$890M'],
                    ['ticker' => 'DOGE', 'name' => 'Dogecoin', 'price' => 0.085, 'change_percent' => 1.1, 'volume' => '$650M'],
                ],
                'most_searched' => [
                    ['ticker' => 'SHIB', 'name' => 'Shiba Inu', 'price' => 0.0000098, 'change_percent' => 0.5, 'volume' => '$120M'],
                    ['ticker' => 'LINK', 'name' => 'Chainlink', 'price' => 19.80, 'change_percent' => 3.2, 'volume' => '$210M'],
                    ['ticker' => 'MATIC', 'name' => 'Polygon', 'price' => 0.95, 'change_percent' => 1.5, 'volume' => '$180M'],
                ]
            ],
             'funds' => [
                'popular' => [
                    ['ticker' => 'VANG', 'name' => 'Vanguard Global Stock Index', 'price' => 124.50, 'change_percent' => 0.5, 'volume' => 'N/A'],
                    ['ticker' => 'MSCI', 'name' => 'iShares MSCI World', 'price' => 88.20, 'change_percent' => 0.3, 'volume' => 'N/A'],
                    ['ticker' => 'FUND', 'name' => 'Fundsmith Equity Fund', 'price' => 654.10, 'change_percent' => 0.1, 'volume' => 'N/A'],
                ]
            ]
        ]);
    }
}
