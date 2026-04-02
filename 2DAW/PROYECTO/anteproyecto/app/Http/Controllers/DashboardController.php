<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Transaction;
use App\Models\Asset;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\Financial\ExpenseService;
use App\Services\Analysis\DashboardService;

class DashboardController extends Controller
{
    protected $expenseService;
    protected $dashboardService;

    public function __construct(ExpenseService $expenseService, DashboardService $dashboardService)
    {
        $this->expenseService = $expenseService;
        $this->dashboardService = $dashboardService;
    }

    /**
     * Display the main dashboard with financial summaries.
     */
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();
        $this->expenseService->ensureUserHasCategories($user->id);

        $portfoliosData = $this->dashboardService->getPortfoliosData($user->id);
        $investmentsTotalValue = $portfoliosData->sum('total_value');

        // Totales del mes (Ingresos vs Gastos)
        $monthlyMetrics = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
            ->selectRaw("SUM(CASE WHEN type IN ('income', 'reward', 'gift', 'dividend') THEN amount ELSE 0 END) as income,
                         SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense")
            ->first();

        // Patrimonio Total
        $cashFlow = Transaction::where('user_id', $user->id)
            ->selectRaw("SUM(CASE WHEN type IN ('income', 'sell', 'dividend', 'gift', 'reward', 'transfer_in') THEN amount 
                                  WHEN type IN ('expense', 'buy', 'transfer_out') THEN -amount ELSE 0 END) as cash")
            ->value('cash') ?? 0;

        $history = $this->dashboardService->getNetWorthHistory($user->id);

        return Inertia::render('Dashboard', [
            'summary' => [
                'netWorth' => $cashFlow + $investmentsTotalValue,
                'cash' => $cashFlow,
                'investmentsTotal' => $investmentsTotalValue,
            ],
            'unlinkedAssets' => Asset::where('user_id', $user->id)->whereIn('link_status', ['pending', 'failed'])->get(),
            'portfolios' => $portfoliosData,
            'expenses' => [
                'monthlyTotal' => (float)$monthlyMetrics->expense,
                'monthlyIncome' => (float)$monthlyMetrics->income,
                'ranges' => $this->getExpenseRangesData($user->id, $now),
            ],
            'charts' => [
                'netWorthLabels' => $history['labels'],
                'netWorthData' => $history['values'],
                'portfolioHistory' => $this->dashboardService->getPortfolioHistory($user->id),
            ],
            'recentTransactions' => $this->getRecentTransactions($user->id),
            'allAssetsList' => Asset::where('user_id', $user->id)->select('id', 'name', 'ticker')->get(),
            'categories' => $this->expenseService->getHierarchicalCategories($user->id),
        ]);
    }

    private function getExpenseRangesData($userId, $now)
    {
        $ranges = [
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'all' => [Carbon::parse('1970-01-01'), $now->copy()->endOfDay()],
        ];

        $data = [];
        foreach ($ranges as $key => $dates) {
            $data[$key] = [
                'total' => (float) Transaction::where('user_id', $userId)->where('type', 'expense')->whereBetween('date', $dates)->sum('amount'),
                'byCategory' => Transaction::where('transactions.user_id', $userId)->where('transactions.type', 'expense')->whereBetween('transactions.date', $dates)
                    ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
                    ->select('categories.name as category', DB::raw('SUM(transactions.amount) as total'))
                    ->groupBy('categories.name')->orderByDesc('total')->get()
            ];
        }
        return $data;
    }

    private function getRecentTransactions($userId)
    {
        return Transaction::where('user_id', $userId)->with(['asset.marketAsset', 'category'])
            ->orderBy('date', 'desc')->take(20)->get()->map(fn($tx) => [
                'id' => $tx->id, 'type' => $tx->type, 'amount' => (float)$tx->amount, 'date' => $tx->date->format('Y-m-d'),
                'display_date' => $tx->date->format('d.m'), 'category' => $tx->category ? $tx->category->name : 'Sin categoría',
                'category_id' => $tx->category_id, 'description' => $tx->description, 'asset_name' => $tx->asset?->name,
                'quantity' => $tx->quantity, 'price_per_unit' => $tx->price_per_unit, 'asset_logo' => $tx->asset?->logo,
            ]);
    }

    public function getTransactions(Request $request)
    {
        $user = Auth::user();
        return Transaction::where('user_id', $user->id)->with(['asset.marketAsset', 'category'])
            ->orderBy('date', 'desc')->offset($request->input('offset', 0))->limit($request->input('limit', 20))
            ->get()->map(fn($tx) => [
                'id' => $tx->id, 'type' => $tx->type, 'amount' => (float)$tx->amount, 'date' => $tx->date->format('Y-m-d'),
                'display_date' => $tx->date->format('d.m'), 'category' => $tx->category ? $tx->category->name : 'Sin categoría',
                'category_id' => $tx->category_id, 'description' => $tx->description, 'asset_name' => $tx->asset?->name,
                'quantity' => $tx->quantity, 'price_per_unit' => $tx->price_per_unit, 'asset_logo' => $tx->asset?->logo,
            ]);
    }
}