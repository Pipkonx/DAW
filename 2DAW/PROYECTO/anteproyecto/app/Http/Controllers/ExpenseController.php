<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Portfolio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    /**
     * Muestra la página de análisis de gastos.
     * Permite filtrar por fecha y categoría.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ensure user has categories (Clone if needed)
        if (Category::where('user_id', $user->id)->count() === 0) {
            $systemCategories = Category::whereNull('user_id')->whereNull('parent_id')->with('children')->get();
            foreach ($systemCategories as $systemCat) {
                $newParent = $systemCat->replicate();
                $newParent->user_id = $user->id;
                $newParent->save();
                foreach ($systemCat->children as $systemChild) {
                    $newChild = $systemChild->replicate();
                    $newChild->user_id = $user->id;
                    $newChild->parent_id = $newParent->id;
                    $newChild->save();
                }
            }
        }

        // Fetch categories hierarchically
        $allCategories = Category::where('user_id', $user->id)
            ->orderBy('usage_count', 'desc')
            ->orderBy('name')
            ->get();

        $categories = $allCategories->whereNull('parent_id')->map(function ($parent) use ($allCategories) {
            $parent->children = $allCategories->where('parent_id', $parent->id)->values();
            return $parent;
        })->values();

        // 1. Determinar rango de fechas (Default: Mes actual)
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay() 
            : Carbon::now()->startOfMonth();
            
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay() 
            : Carbon::now()->endOfMonth();

        // Asegurar que start <= end
        if ($startDate->gt($endDate)) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        // 2. Query Base (Ingresos y Gastos)
        // Filtramos transacciones que NO sean inversiones directas (compra/venta de activos)
        // Aunque el usuario podría querer ver dividendos como ingreso, etc.
        // Por simplicidad: expense, income, transfer_out (gasto), transfer_in (ingreso), dividend (ingreso), gift (ingreso)
        
        $baseQuery = Transaction::where('transactions.user_id', $user->id)
            ->whereBetween('transactions.date', [$startDate, $endDate]);

        // Clonamos para gastos
        $expenseQuery = (clone $baseQuery)->where(function($q) {
            $q->whereIn('transactions.type', ['expense', 'transfer_out']);
        });

        // Clonamos para ingresos
        $incomeQuery = (clone $baseQuery)->where(function($q) {
            $q->whereIn('transactions.type', ['income', 'transfer_in', 'dividend', 'gift', 'reward']);
        });

        // Clonamos para lista completa (Ingresos + Gastos)
        $allQuery = (clone $baseQuery)->where(function($q) {
            $q->whereIn('transactions.type', ['expense', 'transfer_out', 'income', 'transfer_in', 'dividend', 'gift', 'reward']);
        });

        // 3. Métricas KPI
        $totalExpense = $expenseQuery->sum('amount');
        $totalIncome = $incomeQuery->sum('amount');
        $netSavings = $totalIncome - $totalExpense;

        $daysDiff = $startDate->diffInDays($endDate) + 1;
        $avgDailyExpense = $daysDiff > 0 ? $totalExpense / $daysDiff : 0;
        
        // 4. Distribución por Categoría de GASTOS (Gráfico Donut)
        $expensesByCategory = (clone $expenseQuery)
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('categories.name as category_name', DB::raw('SUM(transactions.amount) as total'))
            ->where('transactions.user_id', $user->id)
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->get();

        // 5. Evolución Diaria (Gráfico Línea Comparativa)
        // Datos de gastos por día
        $dailyExpenses = (clone $expenseQuery)
            ->selectRaw('DATE(date) as day, SUM(amount) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        // Datos de ingresos por día
        $dailyIncome = (clone $incomeQuery)
            ->selectRaw('DATE(date) as day, SUM(amount) as total')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        $trendLabels = [];
        $trendDataExpenses = [];
        $trendDataIncome = [];
        
        $current = $startDate->copy();
        while ($current->lte($endDate)) {
            $dateStr = $current->format('Y-m-d');
            $trendLabels[] = $current->format('d M');
            $trendDataExpenses[] = $dailyExpenses->has($dateStr) ? $dailyExpenses[$dateStr]->total : 0;
            $trendDataIncome[] = $dailyIncome->has($dateStr) ? $dailyIncome[$dateStr]->total : 0;
            $current->addDay();
        }

        // 6. Nuevo: Ahorro vs Gano vs Gasto Mensual (Año actual)
        $yearStart = Carbon::now()->startOfYear();
        $yearEnd = Carbon::now()->endOfYear();

        $monthlyStats = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$yearStart, $yearEnd])
            ->selectRaw('
                MONTH(date) as month,
                SUM(CASE WHEN type IN ("income", "transfer_in", "dividend", "gift", "reward") THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type IN ("expense", "transfer_out") THEN amount ELSE 0 END) as expense
            ')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $monthlyLabels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        $monthlyIncomeData = [];
        $monthlyExpenseData = [];
        $monthlySavingsData = [];

        for ($i = 1; $i <= 12; $i++) {
            $stat = $monthlyStats->get($i);
            $inc = $stat ? (float)$stat->income : 0;
            $exp = $stat ? (float)$stat->expense : 0;
            $monthlyIncomeData[] = $inc;
            $monthlyExpenseData[] = $exp;
            $monthlySavingsData[] = $inc - $exp;
        }

        // 7. Lista de Transacciones (Paginada)
        $transactions = (clone $allQuery)
            ->with('category')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString()
            ->through(function ($tx) {
                return [
                    'id' => $tx->id,
                    'type' => $tx->type,
                    'amount' => (float)$tx->amount,
                    'date' => $tx->date->format('Y-m-d'),
                    'display_date' => $tx->date->format('d/m/Y'),
                    'category' => $tx->category ? $tx->category->name : 'Sin categoría',
                    'category_id' => $tx->category_id,
                    'description' => $tx->description,
                ];
            });

        // 7. Datos Auxiliares
        $portfolios = Portfolio::where('user_id', $user->id)->select('id', 'name')->get();

        return Inertia::render('Expenses/Index', [
            'filters' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_expense' => $totalExpense,
                'total_income' => $totalIncome,
                'net_savings' => $netSavings,
                'avg_daily_expense' => $avgDailyExpense,
                'days_count' => $daysDiff
            ],
            'charts' => [
                'categories' => [
                    'labels' => $expensesByCategory->pluck('category_name'),
                    'data' => $expensesByCategory->pluck('total'),
                ],
                'trend' => [
                    'labels' => $trendLabels,
                    'expenses' => $trendDataExpenses,
                    'income' => $trendDataIncome,
                ],
                'monthly' => [
                    'labels' => $monthlyLabels,
                    'income' => $monthlyIncomeData,
                    'expense' => $monthlyExpenseData,
                    'savings' => $monthlySavingsData,
                ]
            ],
            'transactions' => $transactions,
            'portfolios' => $portfolios,
            'categories' => $categories,
        ]);
    }
}
