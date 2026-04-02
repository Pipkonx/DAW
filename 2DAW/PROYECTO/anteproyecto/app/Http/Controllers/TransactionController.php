<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Asset;
use App\Models\Portfolio;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Services\Analysis\PerformanceService;

class TransactionController extends Controller
{
    protected $performanceService;

    public function __construct(PerformanceService $performanceService)
    {
        $this->performanceService = $performanceService;
    }

    /**
     * Display the Net Worth / Transactions dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $portfolioId = $request->input('portfolio_id', 'aggregated');
        $assetId = $request->input('asset_id');
        $timeframe = $request->input('timeframe', 'MAX');

        // Initial Data Fetching
        $firstTransaction = Transaction::where('user_id', $user->id)->orderBy('date', 'asc')->first();
        $minDate = $firstTransaction ? $firstTransaction->date->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        $portfolios = $this->getUserPortfolios($user);
        $assets = $this->getUserAssets($user, $portfolioId);
        $assetIds = $assets->pluck('id');

        // KPI Summaries
        $summary = $this->calculateSummary($assets, $user->id);

        // Transactions History (Paginated)
        $transactions = $this->getTransactionsQuery($user, $portfolioId, $assetId, $assetIds)
            ->paginate(15)
            ->withQueryString();

        // Analytical Data (Performance & Allocations)
        $chartData = $this->performanceService->getChartData($user->id, $assetIds, $timeframe, $summary['current_value']);
        $allocations = $this->getAllocations($assets);

        return Inertia::render('Transactions/Index', [
            'portfolios' => $portfolios,
            'selectedPortfolioId' => $portfolioId,
            'selectedAssetId' => $assetId,
            'summary' => $summary,
            'assets' => $assets,
            'transactions' => $transactions,
            'chart' => [
                'labels' => $chartData['labels'],
                'data' => $chartData['data'],
                'invested' => $chartData['invested'],
                'period_pl_value' => $chartData['period_pl_value'],
                'period_pl_percent' => is_nan($chartData['period_pl_percent']) ? 0 : $chartData['period_pl_percent'],
            ],
            'allocations' => $allocations,
            'filters' => ['timeframe' => $timeframe],
            'minDate' => $minDate,
        ]);
    }

    private function getUserPortfolios($user)
    {
        $portfolios = Portfolio::where('user_id', $user->id)
            ->withCount('assets')
            ->with(['assets'])
            ->get();
            
        return $portfolios->each(function ($portfolio) {
            $portfolio->total_value = $portfolio->assets->sum(function ($asset) {
                return $asset->quantity * ($asset->current_price ?? $asset->avg_buy_price);
            });
            $portfolio->unsetRelation('assets');
        });
    }

    private function getUserAssets($user, $portfolioId)
    {
        $query = Asset::where('user_id', $user->id)->with('marketAsset');
        if ($portfolioId !== 'aggregated') $query->where('portfolio_id', $portfolioId);
        
        $assets = $query->get();
        
        // Self-healing: Recalcular métricas si detectamos inconsistencias
        $assets->each(function ($asset) {
            if ($asset->quantity > 0 && $asset->avg_buy_price == 0 && $asset->transactions()->exists()) {
                $asset->recalculateMetrics();
            }
        });

        return $assets;
    }

    private function calculateSummary($assets, $userId)
    {
        $totalInvested = $assets->sum('total_invested');
        $currentValue = $assets->sum('current_value');
        $totalLiquid = BankAccount::where('user_id', $userId)->sum('balance');

        $totalPL = $currentValue - $totalInvested;
        $totalPLPercent = ($totalInvested > 0) ? ($totalPL / $totalInvested) * 100 : 0;

        return [
            'total_invested' => $totalInvested ?? 0,
            'current_value' => $currentValue ?? 0,
            'total_pl' => $totalPL ?? 0,
            'total_pl_percent' => is_nan($totalPLPercent) ? 0 : $totalPLPercent,
            'total_net_worth' => $currentValue + $totalLiquid,
            'liquid_balance' => $totalLiquid ?? 0,
        ];
    }

    /**
     * Reusable transaction query logic.
     */
    public function getTransactionsQuery($user, $portfolioId, $assetId, $assetIds)
    {
        $query = Transaction::where('user_id', $user->id)->with(['asset.marketAsset', 'portfolio']);
        $investmentTypes = ['buy', 'sell', 'dividend', 'reward', 'gift', 'staking', 'interest', 'coupon'];
        $query->whereIn('type', $investmentTypes);

        if ($assetId) {
            $ids = is_string($assetId) ? explode(',', $assetId) : (array)$assetId;
            $query->whereIn('asset_id', $ids);
        } elseif ($portfolioId !== 'aggregated') {
            $query->where(function($q) use ($portfolioId, $assetIds) {
                $q->where('portfolio_id', $portfolioId)->orWhereIn('asset_id', $assetIds);
            });
        } else {
            $query->where(function($q) use ($assetIds) {
                $q->whereNotNull('portfolio_id')->orWhereIn('asset_id', $assetIds);
            });
        }

        return $query->orderBy('date', 'desc')->orderBy('created_at', 'desc');
    }

    private function getAllocations($assets)
    {
        return [
            'type' => $this->performanceService->getAllocation($assets, 'type'),
            'sector' => $this->performanceService->getAllocation($assets, 'sector'),
            'industry' => $this->performanceService->getAllocation($assets, 'industry'),
            'region' => $this->performanceService->getAllocation($assets, 'region'),
            'country' => $this->performanceService->getAllocation($assets, 'country'),
            'currency_code' => $this->performanceService->getAllocation($assets, 'currency_code'),
            'asset' => $assets->map(fn($a) => ['label' => $a->name, 'value' => $a->current_value, 'color' => $a->color]),
        ];
    }
}
