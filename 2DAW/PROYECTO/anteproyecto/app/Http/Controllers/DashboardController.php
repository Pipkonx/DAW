<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\Asset;
use App\Models\Portfolio;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Muestra el panel principal con resumen financiero detallado y segmentado.
     */
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();

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

        // ---------------------------------------------------------
        // 1. CARTERAS E INVERSIONES
        // ---------------------------------------------------------
        $portfolios = Portfolio::where('user_id', $user->id)
            ->with(['assets' => function ($query) {
                // Incluimos activos con cantidad > 0 para visualización limpia
                $query->where('quantity', '>', 0);
            }])
            ->get();

        // Calcular valor total de inversiones y por cartera
        $investmentsTotalValue = 0;
        $portfoliosData = $portfolios->map(function ($portfolio) use (&$investmentsTotalValue) {
            $portfolioValue = $portfolio->assets->sum(function ($asset) {
                return $asset->current_value;
            });
            $portfolioCost = $portfolio->assets->sum(function ($asset) {
                return $asset->total_invested;
            });
            $investmentsTotalValue += $portfolioValue;

            return [
                'id' => $portfolio->id,
                'name' => $portfolio->name,
                'description' => $portfolio->description,
                'total_value' => $portfolioValue,
                'total_cost' => $portfolioCost,
                'yield' => $portfolioCost > 0 ? (($portfolioValue - $portfolioCost) / $portfolioCost) * 100 : 0,
                'assets' => $portfolio->assets->map(function ($asset) {
                    return [
                        'id' => $asset->id,
                        'name' => $asset->name,
                        'ticker' => $asset->ticker,
                        'type' => $asset->type,
                        'quantity' => $asset->quantity,
                        'current_price' => $asset->current_price,
                        'avg_buy_price' => $asset->avg_buy_price,
                        'current_value' => $asset->current_value,
                        'profit_loss_pct' => $asset->profit_loss_percentage,
                        'color' => $asset->color,
                    ];
                }),
            ];
        });

        // Activos huérfanos (sin cartera asignada)
        $orphanAssets = Asset::where('user_id', $user->id)
            ->whereNull('portfolio_id')
            ->where('quantity', '>', 0)
            ->get();
        
        if ($orphanAssets->count() > 0) {
            $orphanValue = $orphanAssets->sum(function($a) { return $a->current_value; });
            $orphanCost = $orphanAssets->sum(function($a) { return $a->total_invested; });
            $investmentsTotalValue += $orphanValue;
            
            $portfoliosData->push([
                'id' => 'orphan',
                'name' => 'Sin Cartera',
                'description' => 'Activos no asignados',
                'total_value' => $orphanValue,
                'total_cost' => $orphanCost,
                'yield' => $orphanCost > 0 ? (($orphanValue - $orphanCost) / $orphanCost) * 100 : 0,
                'assets' => $orphanAssets->map(function ($asset) {
                    return [
                        'id' => $asset->id,
                        'name' => $asset->name,
                        'ticker' => $asset->ticker,
                        'type' => $asset->type,
                        'quantity' => $asset->quantity,
                        'current_price' => $asset->current_price,
                        'avg_buy_price' => $asset->avg_buy_price,
                        'current_value' => $asset->current_value,
                        'profit_loss_pct' => $asset->profit_loss_percentage,
                        'color' => $asset->color,
                    ];
                }),
            ]);
        }

        // ---------------------------------------------------------
        // 2. GASTOS E INGRESOS (DIARIOS / MENSUALES)
        // ---------------------------------------------------------
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Totales del mes
        $monthlyMetrics = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->selectRaw("
                SUM(CASE WHEN type IN ('income', 'reward', 'gift', 'dividend') THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
            ")
            ->first();

        $monthlyIncome = $monthlyMetrics->income ?? 0;
        $monthlyExpense = $monthlyMetrics->expense ?? 0;

        // Desglose de gastos por categoría (para gráfica de quesitos)
        $expensesByCategory = Transaction::where('transactions.user_id', $user->id)
            ->where('transactions.type', 'expense')
            ->whereBetween('transactions.date', [$startOfMonth, $endOfMonth])
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('categories.name as category_name', DB::raw('SUM(transactions.amount) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->get();

        // ---------------------------------------------------------
        // 3. PATRIMONIO TOTAL (NET WORTH)
        // ---------------------------------------------------------
        // Flujo de caja acumulado (ahorros históricos disponibles)
        $cashFlow = Transaction::where('user_id', $user->id)
            ->selectRaw("
                SUM(CASE 
                    WHEN type IN ('income', 'sell', 'dividend', 'gift', 'reward', 'transfer_in') THEN amount 
                    WHEN type IN ('expense', 'buy', 'transfer_out') THEN -amount 
                    ELSE 0 
                END) as total_cash
            ")
            ->value('total_cash') ?? 0;

        $netWorth = $cashFlow + $investmentsTotalValue;

        // ---------------------------------------------------------
        // 4. HISTORIAL Y TRANSACCIONES
        // ---------------------------------------------------------
        
        // Gráfica de Patrimonio (Últimos 6 meses)
        $chartLabels = [];
        $chartDataValues = [];
        $portfolioHistory = []; // [portfolio_id => [val1, val2...]]
        
        // Inicializar arrays de historial por cartera
        foreach ($portfolios as $p) {
            $portfolioHistory[$p->id] = [];
        }

        for ($i = 5; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i)->endOfMonth();
            $chartLabels[] = $date->format('M');
            
            // Efectivo a la fecha
            $cashAtDate = Transaction::where('user_id', $user->id)
                ->where('date', '<=', $date)
                ->selectRaw("
                    SUM(CASE 
                        WHEN type IN ('income', 'sell', 'dividend', 'gift', 'reward', 'transfer_in') THEN amount 
                        WHEN type IN ('expense', 'buy', 'transfer_out') THEN -amount 
                        ELSE 0 
                    END) as total_cash
                ")
                ->value('total_cash') ?? 0;

            // Valor inversiones a la fecha (aprox) por cartera
            $assetsValueAtDate = 0;
            $allAssets = Asset::where('user_id', $user->id)->get();
            
            // Calculamos valor por cartera en este punto del tiempo
            foreach ($portfolios as $p) {
                $portfolioValAtDate = 0;
                foreach ($p->assets as $asset) {
                     $qtyAtDate = Transaction::where('user_id', $user->id)
                        ->where('asset_id', $asset->id)
                        ->where('date', '<=', $date)
                        ->selectRaw("SUM(CASE WHEN type = 'buy' THEN quantity WHEN type = 'sell' THEN -quantity ELSE 0 END) as qty")
                        ->value('qty') ?? 0;
                    
                    // Usamos precio actual como aproximación (MVP) o avg_buy_price si se prefiere coste
                    $portfolioValAtDate += $qtyAtDate * ($asset->current_price ?? 0);
                }
                $portfolioHistory[$p->id][] = round($portfolioValAtDate, 2);
                $assetsValueAtDate += $portfolioValAtDate;
            }

            // Sumar activos huérfanos si los hay
            if (isset($orphanAssets)) {
                 foreach ($orphanAssets as $asset) {
                     $qtyAtDate = Transaction::where('user_id', $user->id)
                        ->where('asset_id', $asset->id)
                        ->where('date', '<=', $date)
                        ->selectRaw("SUM(CASE WHEN type = 'buy' THEN quantity WHEN type = 'sell' THEN -quantity ELSE 0 END) as qty")
                        ->value('qty') ?? 0;
                    $assetsValueAtDate += $qtyAtDate * ($asset->current_price ?? 0);
                 }
            }

            $chartDataValues[] = round($cashAtDate + $assetsValueAtDate, 2);
        }

        // Últimas transacciones (Editables)
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with(['asset', 'category'])
            ->orderBy('date', 'desc')
            ->take(20) // Aumentamos a 20 para mejor visualización
            ->get()
            ->map(function ($tx) {
                return [
                    'id' => $tx->id,
                    'type' => $tx->type,
                    'amount' => (float)$tx->amount,
                    'date' => $tx->date->format('Y-m-d'), // Formato input date
                    'display_date' => $tx->date->format('d/m/Y'),
                    'category' => $tx->category ? $tx->category->name : 'Sin categoría',
                    'category_id' => $tx->category_id,
                    'description' => $tx->description,
                    'asset_name' => $tx->asset?->name,
                    'quantity' => $tx->quantity,
                    'price_per_unit' => $tx->price_per_unit,
                    'asset_logo' => $tx->asset?->ticker ? 'https://logo.clearbit.com/' . strtolower($tx->asset->ticker) . '.com' : null, // Intento de logo simple
                ];
            });

        return inertia('Dashboard', [
            'summary' => [
                'netWorth' => $netWorth,
                'cash' => $cashFlow,
                'investmentsTotal' => $investmentsTotalValue,
            ],
            'portfolios' => $portfoliosData,
            'expenses' => [
                'monthlyTotal' => $monthlyExpense,
                'monthlyIncome' => $monthlyIncome,
                'byCategory' => [
                    'labels' => $expensesByCategory->pluck('category_name'),
                    'data' => $expensesByCategory->pluck('total'),
                ],
            ],
            'charts' => [
                'netWorthLabels' => $chartLabels,
                'netWorthData' => $chartDataValues,
                'portfolioHistory' => $portfolioHistory, // Datos históricos por cartera
            ],
            'recentTransactions' => $recentTransactions,
            'allAssetsList' => Asset::where('user_id', $user->id)->select('id', 'name', 'ticker')->get(),
            'categories' => $categories,
        ]);
    }
}