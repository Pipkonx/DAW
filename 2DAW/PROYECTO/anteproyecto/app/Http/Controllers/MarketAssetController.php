<?php

namespace App\Http\Controllers;

use App\Models\MarketAsset;
use App\Models\Asset;
use App\Models\Post;
use App\Services\MarketDataService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MarketAssetController extends Controller
{
    protected $marketDataService;

    public function __construct(MarketDataService $marketDataService)
    {
        $this->marketDataService = $marketDataService;
    }

    public function show($ticker)
    {
        // 1. Encontrar o Sincronizar automáticamente para evitar 404 (ej: NVDA)
        $marketAsset = $this->marketDataService->ensureAssetSynced($ticker);

        if (!$marketAsset) {
            abort(404, "Activo no encontrado en los mercados conocidos.");
        }

        // Latest Price
        $price = $this->marketDataService->getLatestPrice($marketAsset);

        // Perfil detallado (Sector, Industria, Descripción) para Datos Clave y Heatmap
        $profile = $this->marketDataService->getAssetProfile($marketAsset);
        
        // Chart Data (Default 1Y / 365 days)
        $chartData = $this->marketDataService->getChartData($marketAsset, 365);

        // User Position (if any) - Aggregate across all portfolios
        $userAssets = Asset::where('user_id', Auth::id())
            ->where('market_asset_id', $marketAsset->id)
            ->get();
        
        $portfolioData = null;
        $allTransactions = collect();
        $latestTransactions = [];

        if ($userAssets->isNotEmpty()) {
            $totalQuantity = 0;
            $totalInvested = 0;
            $totalCurrentValue = 0;
            $totalProfitLoss = 0;
            $totalRealizedGain = 0;
            $totalFees = 0;
            $totalTax = 0;

            foreach ($userAssets as $asset) {
                $totalQuantity += $asset->quantity;
                $totalInvested += $asset->total_invested;
                $totalCurrentValue += $asset->current_value;
                $totalProfitLoss += $asset->profit_loss;

                $transactions = $asset->transactions()->orderBy('date', 'desc')->get();
                $allTransactions = $allTransactions->concat($transactions);

                $totalFees += $transactions->sum(fn($tx) => ($tx->fees ?: 0) + ($tx->exchange_fees ?: 0));
                $totalTax += $transactions->sum('tax') ?: 0;

                // Calculate Realized Gain for this asset
                $sellTransactions = $transactions->where('type', 'sell');
                foreach ($sellTransactions as $sold) {
                    $costBasis = $sold->quantity * ($asset->avg_buy_price ?: 0);
                    $sellValue = $sold->quantity * $sold->price_per_unit;
                    $totalRealizedGain += ($sellValue - $costBasis);
                }
            }

            $latestTransactions = $allTransactions->sortByDesc('date')->take(10)->values();
            
            $avgBuyPrice = $totalQuantity > 0 ? ($totalInvested / $totalQuantity) : 0;
            $profitLossPercentage = $totalInvested > 0 ? (($totalCurrentValue - $totalInvested) / $totalInvested) * 100 : 0;

            $portfolioData = [
                'quantity' => $totalQuantity,
                'avg_buy_price' => $avgBuyPrice,
                'total_invested' => $totalInvested,
                'current_value' => $totalCurrentValue,
                'profit_loss' => $totalProfitLoss,
                'profit_loss_percentage' => $profitLossPercentage,
                'realized_gain' => $totalRealizedGain,
                'total_fees' => $totalFees,
                'total_tax' => $totalTax,
                'total_return' => $totalProfitLoss + $totalRealizedGain - $totalFees - $totalTax,
            ];
        }

        // Social Posts
        $posts = Post::where('market_asset_id', $marketAsset->id)
            ->with(['user', 'likes', 'comments.user', 'comments.likes'])
            ->withCount(['likes', 'comments', 'reposts', 'bookmarks'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Assets/Show', [
            'marketAsset' => array_merge($marketAsset->toArray(), $profile ?? [], [
                'current_price' => (float)$price,
                'type_label' => $marketAsset->type_label,
                'logo_url' => $profile['image'] ?? "https://financialmodelingprep.com/image-stock/{$marketAsset->ticker}.png",
            ]),
            'chartData' => $chartData,
            'userPosition' => $portfolioData,
            'latestTransactions' => $latestTransactions,
            'posts' => $posts,
            'filters' => [
                'ticker' => $ticker
            ]
        ]);
    }

    /**
     * API endpoint to get chart data for different ranges
     */
    public function getChartRange(Request $request, $ticker)
    {
        $days = $request->query('days', 365);
        $data = $this->marketDataService->getChartData($ticker, $days);
        return response()->json($data);
    }
}
