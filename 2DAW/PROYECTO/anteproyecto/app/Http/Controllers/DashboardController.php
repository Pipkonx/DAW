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
use App\Models\BankAccount;

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
     * Muestra el panel de control principal con los resúmenes financieros consolidados.
     * 
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();
        $this->expenseService->ensureUserHasCategories($user->id);

        $portfoliosData = $this->dashboardService->getPortfoliosData($user->id);
        $investmentsTotalValue = $portfoliosData->sum('total_value');
        $investmentsTotalCost = $portfoliosData->sum('total_cost');
        $investmentsYield = $investmentsTotalCost > 0 ? (($investmentsTotalValue - $investmentsTotalCost) / $investmentsTotalCost) * 100 : 0;

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

        // Ahorros en planificación financiera (Cuentas remuneradas, etc)
        $bankBalance = BankAccount::where('user_id', $user->id)->sum('balance') ?? 0;
        $months = request('months', null); // default to MAX (null)
        $history = $this->dashboardService->getNetWorthHistory($user->id, $months);

        $filter = request('filter', 'all');

        return Inertia::render('Dashboard', [
            'summary' => [
                'netWorth' => $cashFlow + $investmentsTotalValue,
                'cash' => $cashFlow,
                'investmentsTotal' => $investmentsTotalValue,
                'investmentsCost' => $investmentsTotalCost,
                'investmentsYield' => $investmentsYield,
                'bankBalance' => $bankBalance,
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
                'netWorthYields' => $history['yields'],
                'portfolioHistory' => $this->dashboardService->getPortfolioHistory($user->id, $months),
                'annualPerformance' => $this->dashboardService->getAnnualPerformance($user->id),
                'allocation' => [
                    'labels' => ['Invertido', 'Liquidez'],
                    'values' => [
                        (float)$investmentsTotalValue,
                        (float)max(0, $cashFlow),
                    ]
                ]
            ],
            'recentTransactions' => $this->getRecentTransactions($user->id, $filter),
            'currentFilter' => $filter,
            'allAssetsList' => Asset::where('user_id', $user->id)->select('id', 'name', 'ticker')->get(),
            'categories' => $this->expenseService->getHierarchicalCategories($user->id),
            'selectedMonths' => $months,
        ]);
    }

    private function getExpenseRangesData($userId, $now)
    {
        $firstTx = Transaction::where('user_id', $userId)->orderBy('date', 'asc')->first();
        $allStart = $firstTx ? Carbon::parse($firstTx->date) : $now->copy()->subYear();

        $ranges = [
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'all' => [$allStart->startOfDay(), $now->copy()->endOfDay()],
        ];

        $data = [];
        foreach ($ranges as $key => $dates) {
            $data[$key] = [
                'start' => $dates[0]->format('Y-m-d'),
                'end' => $dates[1]->format('Y-m-d'),
                'total' => (float) Transaction::where('user_id', $userId)->where('type', 'expense')->whereBetween('date', $dates)->sum('amount'),
                'byCategory' => Transaction::where('transactions.user_id', $userId)->where('transactions.type', 'expense')->whereBetween('transactions.date', $dates)
                    ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
                    ->select('categories.name as category', DB::raw('SUM(transactions.amount) as total'))
                    ->groupBy('categories.name')->orderByDesc('total')->get()
            ];
        }
        return $data;
    }

    /**
     * Obtiene transacciones recientes formateadas para el listado del panel.
     */
    private function getRecentTransactions($userId, $filter = 'all')
    {
        $query = Transaction::where('user_id', $userId)->with(['asset.marketAsset', 'category']);

        if ($filter === 'income') {
            $query->whereIn('type', ['income', 'reward', 'dividend', 'gift', 'transfer_in']);
        } elseif ($filter === 'expense') {
            $query->whereIn('type', ['expense', 'transfer_out']);
        } elseif ($filter === 'investment') {
            $query->whereIn('type', ['buy', 'sell']);
        }

        return $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->take(20)->get()->map(fn($tx) => [
            'id' => $tx->id, 
            'type' => $tx->type, 
            'amount' => (float)$tx->amount, 
            'date' => $tx->date->format('Y-m-d'),
            'display_date' => $tx->date->format('d.m'), 
            'category' => $tx->category ? $tx->category->name : 'Sin categoría',
            'category_id' => $tx->category_id, 
            'description' => $tx->description, 
            'asset_name' => $tx->asset?->name,
            'quantity' => $tx->quantity, 
            'price_per_unit' => $tx->price_per_unit, 
            'asset_logo' => $tx->asset?->logo,
        ]);
    }

    /**
     * Endpoint API para el scroll infinito de transacciones.
     */
    public function getTransactions(Request $request)
    {
        $user = Auth::user();
        $filter = $request->input('filter', 'all');

        $query = Transaction::where('user_id', $user->id)->with(['asset.marketAsset', 'category']);

        if ($filter === 'income') {
            $query->whereIn('type', ['income', 'reward', 'dividend', 'gift', 'transfer_in']);
        } elseif ($filter === 'expense') {
            $query->whereIn('type', ['expense', 'transfer_out']);
        } elseif ($filter === 'investment') {
            $query->whereIn('type', ['buy', 'sell']);
        }

        return $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')
            ->offset($request->input('offset', 0))
            ->limit($request->input('limit', 20))
            ->get()->map(fn($tx) => [
                'id' => $tx->id, 
                'type' => $tx->type, 
                'amount' => (float)$tx->amount, 
                'date' => $tx->date->format('Y-m-d'),
                'display_date' => $tx->date->format('d.m'), 
                'category' => $tx->category ? $tx->category->name : 'Sin categoría',
                'category_id' => $tx->category_id, 
                'description' => $tx->description, 
                'asset_name' => $tx->asset?->name,
                'quantity' => $tx->quantity, 
                'price_per_unit' => $tx->price_per_unit, 
                'asset_logo' => $tx->asset?->logo,
            ]);
    }
}