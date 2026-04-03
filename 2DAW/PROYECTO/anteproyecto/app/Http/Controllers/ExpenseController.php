<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Transaction;
use App\Models\Portfolio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\Financial\ExpenseService;

class ExpenseController extends Controller
{
    protected $expenseService;

    public function __construct(ExpenseService $expenseService)
    {
        $this->expenseService = $expenseService;
    }

    /**
     * Display the expense analysis page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $this->expenseService->ensureUserHasCategories($user->id);

        $categories = $this->expenseService->getHierarchicalCategories($user->id);
        $year = $request->input('year', Carbon::now()->year);
        
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay() 
            : Carbon::createFromDate($year, 1, 1)->startOfDay();
            
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay() 
            : Carbon::createFromDate($year, 12, 31)->endOfDay();

        if ($startDate->gt($endDate)) {
            $temp = $startDate; $startDate = $endDate; $endDate = $temp;
        }

        $availableYears = $this->getAvailableYears($user->id);

        // Fetch Data via Service
        $monthlyData = $this->expenseService->getMonthlyStats($user->id, $startDate, $endDate);
        $topExpenses = $this->expenseService->getTopItems($user->id, $startDate, $endDate, ['expense', 'transfer_out']);
        $topIncome = $this->expenseService->getTopItems($user->id, $startDate, $endDate, ['income', 'transfer_in', 'dividend', 'gift', 'reward']);

        // Transactions list (Paginated)
        $transactions = $this->getPaginatedTransactions($user->id, $startDate, $endDate, $request);

        return Inertia::render('Expenses/Index', [
            'filters' => ['start_date' => $startDate->format('Y-m-d'), 'end_date' => $endDate->format('Y-m-d'), 'year' => $year],
            'availableYears' => $availableYears,
            'selectedYear' => (int)$year,
            'topExpenses' => $topExpenses,
            'topIncome' => $topIncome,
            'yearStats' => [
                'total_income' => array_sum($monthlyData['income']),
                'total_expense' => array_sum($monthlyData['expense']),
                'savings' => array_sum($monthlyData['savings']),
            ],
            'summary' => $this->calculateSummary($user->id, $startDate, $endDate),
            'charts' => [
                'categories' => $this->getCategoryChartData($user->id, $startDate, $endDate),
                'trend' => $this->getTrendChartData($user->id, $startDate, $endDate),
                'monthly' => $monthlyData,
            ],
            'transactions' => $transactions,
            'portfolios' => Portfolio::where('user_id', $user->id)->select('id', 'name')->get(),
            'categories' => $categories,
        ]);
    }

    private function getAvailableYears($userId)
    {
        $years = Transaction::where('user_id', $userId)->selectRaw('YEAR(date) as year')->distinct()->orderBy('year', 'desc')->pluck('year')->toArray();
        if (!in_array(Carbon::now()->year, $years)) array_unshift($years, Carbon::now()->year);
        return $years;
    }

    private function calculateSummary($userId, $startDate, $endDate)
    {
        $totals = Transaction::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('
                SUM(CASE WHEN type IN ("expense", "transfer_out") THEN amount ELSE 0 END) as total_expense,
                SUM(CASE WHEN type IN ("income", "transfer_in", "dividend", "gift", "reward") THEN amount ELSE 0 END) as total_income
            ')
            ->first();

        $days = $startDate->diffInDays($endDate) + 1;
        return [
            'total_expense' => (float)$totals->total_expense,
            'total_income' => (float)$totals->total_income,
            'net_savings' => $totals->total_income - $totals->total_expense,
            'avg_daily_expense' => $days > 0 ? $totals->total_expense / $days : 0,
            'days_count' => $days
        ];
    }

    private function getCategoryChartData($userId, $startDate, $endDate)
    {
        $data = Transaction::where('transactions.user_id', $userId)
            ->whereBetween('transactions.date', [$startDate, $endDate])
            ->whereIn('transactions.type', ['expense', 'transfer_out'])
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->leftJoin('assets', 'transactions.asset_id', '=', 'assets.id')
            ->select(
                DB::raw('COALESCE(categories.name, assets.name, "Sin categoría") as category_name'), 
                DB::raw('SUM(transactions.amount) as total')
            )
            ->groupBy('category_name')
            ->orderByDesc('total')
            ->get();

        return [
            'labels' => $data->pluck('category_name'),
            'data' => $data->pluck('total'),
        ];
    }

    private function getTrendChartData($userId, $startDate, $endDate)
    {
        $txs = Transaction::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('DATE(date) as day, type, amount')
            ->orderBy('date')
            ->get()
            ->groupBy('day');

        $labels = []; $balance = []; $currentBalance = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            $dateStr = $current->format('Y-m-d');
            $labels[] = $current->format('d M');
            if ($txs->has($dateStr)) {
                foreach ($txs[$dateStr] as $tx) {
                    if (in_array($tx->type, ['income', 'transfer_in', 'dividend', 'gift', 'reward'])) $currentBalance += $tx->amount;
                    else $currentBalance -= $tx->amount;
                }
            }
            $balance[] = $currentBalance;
            $current->addDay();
        }
        return compact('labels', 'balance');
    }

    private function getPaginatedTransactions($userId, $startDate, $endDate, $request)
    {
        $sort = $request->input('sort_by', 'date');
        $direction = $request->input('direction', 'desc');

        $query = Transaction::where('transactions.user_id', $userId)
            ->whereBetween('transactions.date', [$startDate, $endDate])
            ->whereIn('transactions.type', ['expense', 'transfer_out', 'income', 'transfer_in', 'dividend', 'gift', 'reward'])
            ->with(['category', 'asset']);

        if ($sort === 'category') {
            $query->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')->orderBy('categories.name', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        return $query->select('transactions.*')->orderBy('created_at', 'desc')->paginate(15)->withQueryString()->through(fn($tx) => [
            'id' => $tx->id, 
            'type' => $tx->type, 
            'amount' => (float)$tx->amount, 
            'date' => $tx->date->format('Y-m-d'),
            'display_date' => $tx->date->format('d/m/Y'), 
            'category' => $tx->category?->name ?? ($tx->asset?->name ?? 'Sin categoría'),
            'category_id' => $tx->category_id, 
            'description' => $tx->description,
            'asset' => $tx->asset ? [
                'id' => $tx->asset->id,
                'name' => $tx->asset->name,
                'ticker' => $tx->asset->ticker,
                'logo' => $tx->asset->logo,
                'type' => $tx->asset->type,
            ] : null,
        ]);
    }
}
