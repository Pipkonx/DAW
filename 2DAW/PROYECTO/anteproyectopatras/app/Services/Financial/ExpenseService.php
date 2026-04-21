<?php

namespace App\Services\Financial;

use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    /**
     * Asegura que el usuario tenga una copia local de las categorías del sistema.
     */
    public function ensureUserHasCategories($userId)
    {
        if (Category::where('user_id', $userId)->count() === 0) {
            $systemCategories = Category::whereNull('user_id')->whereNull('parent_id')->with('children')->get();
            /** @var \App\Models\Category $systemCat */
            foreach ($systemCategories as $systemCat) {
                /** @var \App\Models\Category $newParent */
                $newParent = $systemCat->replicate();
                $newParent->user_id = $userId;
                $newParent->save();

                /** @var \App\Models\Category $systemChild */
                foreach ($systemCat->children as $systemChild) {
                    /** @var \App\Models\Category $newChild */
                    $newChild = $systemChild->replicate();
                    $newChild->user_id = $userId;
                    $newChild->parent_id = $newParent->id;
                    $newChild->save();
                }
            }
        }
    }

    /**
     * Obtiene las categorías jerárquicas para un usuario.
     */
    public function getHierarchicalCategories($userId)
    {
        $allCategories = Category::where('user_id', $userId)
            ->orderBy('usage_count', 'desc')
            ->orderBy('name')
            ->get();

        return $allCategories->whereNull('parent_id')->map(function ($parent) use ($allCategories) {
            /** @var \App\Models\Category $parent */
            $parent->setAttribute('children', $allCategories->where('parent_id', $parent->id)->values());
            return $parent;
        })->values();
    }

    /**
     * Calcula estadísticas mensuales (ingresos vs gastos) para un rango de fechas.
     */
    public function getMonthlyStats($userId, Carbon $startDate, Carbon $endDate)
    {
        $stats = Transaction::where('user_id', $userId)
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw('
                YEAR(date) as year,
                MONTH(date) as month,
                SUM(CASE WHEN type IN ("income", "transfer_in", "dividend", "gift", "reward") THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type IN ("expense", "transfer_out") THEN amount ELSE 0 END) as expense
            ')
            ->groupBy('year', 'month')
            ->get();

        $labels = [];
        $incomeData = [];
        $expenseData = [];
        $savingsData = [];
        
        $current = $startDate->copy()->startOfMonth();
        $end = $endDate->copy()->startOfMonth();

        while ($current->lte($end)) {
            $label = ($startDate->year === $endDate->year) 
                ? ucfirst($current->translatedFormat('M')) 
                : ucfirst($current->translatedFormat('M y'));
            
            $labels[] = $label;
            
            $stat = $stats->first(fn($item) => $item->year == $current->year && $item->month == $current->month);
            $inc = $stat ? (float)$stat->income : 0;
            $exp = $stat ? (float)$stat->expense : 0;
            
            $incomeData[] = $inc;
            $expenseData[] = $exp;
            $savingsData[] = $inc - $exp;
            
            $current->addMonth();
        }

        return [
            'labels' => $labels,
            'income' => $incomeData,
            'expense' => $expenseData,
            'savings' => $savingsData,
        ];
    }

    /**
     * Obtiene las categorías/descripciones principales para un rango.
     */
    public function getTopItems($userId, $startDate, $endDate, array $types)
    {
        $items = Transaction::where('transactions.user_id', $userId)
            ->whereBetween('transactions.date', [$startDate, $endDate])
            ->whereIn('transactions.type', $types)
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->leftJoin('assets', 'transactions.asset_id', '=', 'assets.id')
            ->select(
                DB::raw('COALESCE(categories.name, assets.name, "Sin categoría") as display_name'),
                DB::raw('SUM(transactions.amount) as total')
            )
            ->groupBy('display_name')
            ->orderByDesc('total')
            ->get();

        return $items->map(function($item) {
            return [
                'category_name' => $item->display_name,
                'total' => (float)$item->total
            ];
        });
    }
}
