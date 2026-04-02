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

        // 1. Determinar rango de fechas y AÑO seleccionado
        $year = $request->input('year', Carbon::now()->year);
        
        // Si no hay rango de fechas explícito, usamos el año completo para los gráficos de resumen
        // Pero para el listado de transacciones, si no hay filtro, podríamos mostrar todo o el año.
        // Mantengamos la lógica actual: si no hay fechas, usa el MES actual por defecto para la lista.
        // Pero el usuario pidió "gráfica permite poner años anteriores".
        // Vamos a cambiar la lógica: Si se selecciona un AÑO, las gráficas muestran ese año.
        // El listado de transacciones mostrará el año completo por defecto si no se especifica rango.
        
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date'))->startOfDay() 
            : Carbon::createFromDate($year, 1, 1)->startOfDay();
            
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date'))->endOfDay() 
            : Carbon::createFromDate($year, 12, 31)->endOfDay();

        // Asegurar que start <= end
        if ($startDate->gt($endDate)) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }
        
        // Obtener años disponibles para el selector
        $availableYears = Transaction::where('user_id', $user->id)
            ->selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();
            
        if (!in_array(Carbon::now()->year, $availableYears)) {
            array_unshift($availableYears, Carbon::now()->year);
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
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->get()
            ->map(function($item) {
                if (empty($item->category_name)) {
                    $item->category_name = 'Sin categoría';
                }
                return $item;
            });
            
        // 5. Evolución Diaria ACUMULATIVA (Saldo a lo largo del periodo)
        // En lugar de mostrar cuánto gasté/ingresé cada día, mostramos cómo evoluciona el "Saldo del Periodo"
        // Empezamos en 0 (o podríamos calcular el saldo inicial, pero para "Evolución Ingresos vs Gastos"
        // suele ser mejor ver el flujo neto acumulado en ese periodo).
        
        $dailyTransactions = (clone $baseQuery)
             ->selectRaw('DATE(date) as day, type, amount')
             ->orderBy('date')
             ->get()
             ->groupBy('day');
             
        $trendLabels = [];
        $trendDataBalance = []; // Línea única de saldo acumulado
        
        $currentBalance = 0;
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            $dateStr = $current->format('Y-m-d');
            $trendLabels[] = $current->format('d M');
            
            if ($dailyTransactions->has($dateStr)) {
                foreach ($dailyTransactions[$dateStr] as $tx) {
                    if (in_array($tx->type, ['income', 'transfer_in', 'dividend', 'gift', 'reward'])) {
                        $currentBalance += $tx->amount;
                    } elseif (in_array($tx->type, ['expense', 'transfer_out'])) {
                        $currentBalance -= $tx->amount;
                    }
                }
            }
            
            $trendDataBalance[] = $currentBalance;
            $current->addDay();
        }

        // 6. Nuevo: Ahorro vs Gano vs Gasto Mensual (Rango Seleccionado)
        $monthlyStats = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('
                YEAR(date) as year,
                MONTH(date) as month,
                SUM(CASE WHEN type IN ("income", "transfer_in", "dividend", "gift", "reward") THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type IN ("expense", "transfer_out") THEN amount ELSE 0 END) as expense
            ')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $monthlyLabels = [];
        $monthlyIncomeData = [];
        $monthlyExpenseData = [];
        $monthlySavingsData = [];
        
        // Iterar mes a mes desde startDate hasta endDate para rellenar huecos
        $currentMonth = $startDate->copy()->startOfMonth();
        $endMonth = $endDate->copy()->startOfMonth();

        while ($currentMonth->lte($endMonth)) {
            $yearKey = $currentMonth->year;
            $monthKey = $currentMonth->month;
            
            // Formato etiqueta: "Ene 23" o solo "Ene" si es mismo año actual
            // Pero para consistencia en rangos largos, mejor usar "Mmm YY"
            // Si el rango es dentro de un mismo año, podríamos simplificar a "Mmm"
            if ($startDate->year === $endDate->year) {
                 $monthlyLabels[] = ucfirst($currentMonth->translatedFormat('M'));
            } else {
                 $monthlyLabels[] = ucfirst($currentMonth->translatedFormat('M y'));
            }

            // Buscar datos
            $stat = $monthlyStats->first(function($item) use ($yearKey, $monthKey) {
                return $item->year == $yearKey && $item->month == $monthKey;
            });

            $inc = $stat ? (float)$stat->income : 0;
            $exp = $stat ? (float)$stat->expense : 0;
            
            $monthlyIncomeData[] = $inc;
            $monthlyExpenseData[] = $exp;
            $monthlySavingsData[] = $inc - $exp;
            
            $currentMonth->addMonth();
        }
        
        // Calcular totales anuales (o del periodo) para estadísticas
        // Si el usuario pidió "estadistica anual", podemos seguir devolviendo el total del periodo seleccionado
        // o calcular el del año en curso aparte. 
        // El frontend muestra "yearStats", que antes era fijo del año. 
        // Ahora debería ser del periodo seleccionado para ser consistente con la gráfica.
        $yearTotalIncome = array_sum($monthlyIncomeData);
        $yearTotalExpense = array_sum($monthlyExpenseData);
        
        // Top Categorías de Gasto del Periodo
        $topExpensesYear = Transaction::where('transactions.user_id', $user->id)
            ->whereBetween('transactions.date', [$startDate, $endDate])
            ->whereIn('transactions.type', ['expense', 'transfer_out'])
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('categories.name as category_name', 'transactions.description', DB::raw('SUM(transactions.amount) as total'))
            ->groupBy('categories.name', 'transactions.description')
            ->orderByDesc('total')
            ->get()
            ->map(function($item) {
                // Si no tiene categoría, usar la descripción o 'Sin categoría'
                if (!$item->category_name) {
                    $item->category_name = $item->description ?: 'Sin categoría';
                } else {
                     if ($item->description) {
                         $item->category_name = $item->description;
                     }
                }
                return $item;
            });
            
        // Re-agrupar por nombre final para sumar duplicados de misma descripción
        $topExpensesYear = $topExpensesYear->groupBy('category_name')->map(function($group) {
             return [
                 'category_name' => $group->first()->category_name,
                 'total' => $group->sum('total')
             ];
        })->values()->sortByDesc('total')->values();


        // Top Categorías de Ingreso del Periodo
        $topIncomeYear = Transaction::where('transactions.user_id', $user->id)
            ->whereBetween('transactions.date', [$startDate, $endDate])
            ->whereIn('transactions.type', ['income', 'transfer_in', 'dividend', 'gift', 'reward'])
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('categories.name as category_name', 'transactions.type', 'transactions.description', DB::raw('SUM(transactions.amount) as total'))
            ->groupBy('categories.name', 'transactions.type', 'transactions.description')
            ->orderByDesc('total')
            ->get()
            ->map(function($item) {
                // Prioridad: Descripción > Categoría > Tipo
                if ($item->description) {
                    $item->category_name = $item->description;
                } elseif (!$item->category_name) {
                    $types = [
                        'income' => 'Ingresos',
                        'transfer_in' => 'Transferencia',
                        'dividend' => 'Dividendos',
                        'gift' => 'Regalos',
                        'reward' => 'Recompensas'
                    ];
                    $item->category_name = $types[$item->type] ?? ucfirst($item->type);
                }
                return $item;
            });
            
        // Agrupar ingresos por nombre final
        $topIncomeYear = $topIncomeYear->groupBy('category_name')->map(function($group) {
            return [
                'category_name' => $group->first()->category_name,
                'total' => $group->sum('total')
            ];
        })->values()->sortByDesc('total')->values();

        // 7. Lista de Transacciones (Paginada y Ordenable)
        $sort = $request->input('sort_by', 'date');
        $direction = $request->input('direction', 'desc');

        $allowedSorts = ['date', 'amount', 'category'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'date';
        }
        if (!in_array($direction, ['asc', 'desc'])) {
            $direction = 'desc';
        }

        $transactionsQuery = (clone $allQuery)->with('category');

        if ($sort === 'category') {
            $transactionsQuery->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
                ->orderBy('categories.name', $direction);
        } else {
            $transactionsQuery->orderBy($sort, $direction);
        }
        
        // Secondary sort for stability
        $transactionsQuery->orderBy('created_at', 'desc');

        $transactions = $transactionsQuery
            ->select('transactions.*') // Ensure we select transactions.* to avoid id conflicts with joins
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
                'year' => $year,
            ],
            'availableYears' => $availableYears,
            'selectedYear' => (int)$year,
            'topExpenses' => $topExpensesYear,
            'topIncome' => $topIncomeYear,
            'yearStats' => [
                'total_income' => $yearTotalIncome,
                'total_expense' => $yearTotalExpense,
                'savings' => $yearTotalIncome - $yearTotalExpense,
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
                    'balance' => $trendDataBalance,
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
