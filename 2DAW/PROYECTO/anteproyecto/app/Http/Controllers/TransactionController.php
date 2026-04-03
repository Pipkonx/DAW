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
        $minDate = $firstTransaction ? Carbon::parse($firstTransaction->date)->format('Y-m-d') : Carbon::now()->format('Y-m-d');

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
        $chartData = $this->performanceService->getChartData($user->id, $assetIds->toArray(), $timeframe, $summary['current_value']);
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
            $portfolio->total_value = (float) $portfolio->assets->sum(function ($asset) {
                return (float) ($asset->quantity * ($asset->current_price ?? $asset->avg_buy_price));
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

        if ($portfolioId === 'aggregated') {
            $assets = $assets->groupBy('ticker')->map(function ($groupedAssets) {
                $base = clone $groupedAssets->first();
                
                if ($groupedAssets->count() > 1) {
                    $totalQty = $groupedAssets->sum('quantity');
                    $totalInvested = $groupedAssets->sum('total_invested');
                    
                    $base->id = $groupedAssets->pluck('id')->implode(',');
                    $base->quantity = $totalQty;
                    $base->avg_buy_price = $totalQty > 0 ? ($totalInvested / $totalQty) : $base->avg_buy_price;
                }
                
                return $base;
            })->values();
        }

        return $assets;
    }

    private function calculateSummary($assets, $userId)
    {
        $totalInvested = $assets->sum('total_invested');
        $currentValue = $assets->sum('current_value');
        $totalLiquid = BankAccount::where('user_id', $userId)->sum('balance');

        $totalPL = $currentValue - $totalInvested;
        $totalPLPercent = ($totalInvested > 0) ? ($totalPL / $totalInvested) * 100 : 0;

        $assetIds = $assets->pluck('id')->toArray();
        $detailed = $this->performanceService->getDetailedBreakdown($userId, $assetIds);
        $annual = $this->performanceService->getAnnualPerformance($userId, $assetIds);

        return [
            'total_invested' => $totalInvested ?? 0,
            'current_value' => $currentValue ?? 0,
            'total_pl' => $totalPL ?? 0,
            'total_pl_percent' => is_nan($totalPLPercent) ? 0 : $totalPLPercent,
            'total_net_worth' => $currentValue + $totalLiquid,
            'liquid_balance' => $totalLiquid ?? 0,
            'detailed' => $detailed,
            'annual' => $annual,
        ];
    }

    /**
     * Display the detailed Performance dashboard.
     */
    public function performance(Request $request)
    {
        $user = Auth::user();
        $portfolioId = $request->input('portfolio_id', 'aggregated');
        $viewType = $request->input('view', 'MAX'); // MAX or specific year

        $portfolios = $this->getUserPortfolios($user);
        $assets = $this->getUserAssets($user, $portfolioId);
        $assetIds = $assets->pluck('id')->toArray();

        $annual = $this->performanceService->getAnnualPerformance($user->id, $assetIds);
        $heatmap = $this->performanceService->getHeatmapData($user->id, $assetIds);
        
        $monthly = null;
        $detailedYear = null;
        
        if ($viewType !== 'MAX' && is_numeric($viewType)) {
            $detailedYear = (int)$viewType;
            $monthly = $this->performanceService->getMonthlyPerformance($user->id, $assetIds, $detailedYear);
        }

        $detailed = $this->performanceService->getDetailedBreakdown($user->id, $assetIds, $detailedYear);

        return Inertia::render('Transactions/Performance', [
            'portfolios' => $portfolios,
            'selectedPortfolioId' => $portfolioId,
            'annual' => $annual,
            'monthly' => $monthly,
            'heatmap' => $heatmap,
            'detailed' => $detailed,
            'viewType' => $viewType,
        ]);
    }

    /**
     * Muestra la vista de análisis inmersivo de Distribución (Allocation).
     */
    public function allocation(Request $request)
    {
        $user = Auth::user();
        $portfolioId = $request->input('portfolio_id', 'aggregated');
        
        $portfolios = $this->getUserPortfolios($user);
        $assets = $this->getUserAssets($user, $portfolioId);

        // Precalculamos las plusvalías simples para enviarlas al frontend
        // para que puedan totalizarse según las agrupaciones dinámicas.
        $assets->each(function($asset) {
            $asset->total_pl = $asset->current_value - $asset->total_invested;
            if ($asset->marketAsset) {
                $asset->type = $asset->marketAsset->type;
                $asset->sector = $asset->marketAsset->sector;
                $asset->industry = $asset->marketAsset->industry;
                $asset->region = $asset->marketAsset->region;
                $asset->country = $asset->marketAsset->country;
                $asset->currency_code = $asset->marketAsset->currency_code;
            }
        });

        return Inertia::render('Transactions/Allocation', [
            'portfolios' => $portfolios,
            'selectedPortfolioId' => $portfolioId,
            'assets' => $assets
        ]);
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
