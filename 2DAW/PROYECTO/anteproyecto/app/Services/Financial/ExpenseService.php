<?php

namespace App\Services\Financial;

use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    /**
     * Ensures the user has a local copy of system categories.
     */
    public function ensureUserHasCategories($userId)
    {
        if (Category::where('user_id', $userId)->count() === 0) {
            $systemCategories = Category::whereNull('user_id')->whereNull('parent_id')->with('children')->get();
            foreach ($systemCategories as $systemCat) {
                $newParent = $systemCat->replicate();
                $newParent->user_id = $userId;
                $newParent->save();
                foreach ($systemCat->children as $systemChild) {
                    $newChild = $systemChild->replicate();
                    $newChild->user_id = $userId;
                    $newChild->parent_id = $newParent->id;
                    $newChild->save();
                }
            }
        }
    }

    /**
     * Fetch hierarchical categories for a user.
     */
    public function getHierarchicalCategories($userId)
    {
        $allCategories = Category::where('user_id', $userId)
            ->orderBy('usage_count', 'desc')
            ->orderBy('name')
            ->get();

        return $allCategories->whereNull('parent_id')->map(function ($parent) use ($allCategories) {
            $parent->children = $allCategories->where('parent_id', $parent->id)->values();
            return $parent;
        })->values();
    }

    /**
     * Calculate monthly statistics (income vs expense) for a date range.
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
     * Get top categories/descriptions for a range.
     */
    public function getTopItems($userId, $startDate, $endDate, array $types)
    {
        $items = Transaction::where('transactions.user_id', $userId)
            ->whereBetween('transactions.date', [$startDate, $endDate])
            ->whereIn('transactions.type', $types)
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->select('categories.name as category_name', 'transactions.description', DB::raw('SUM(transactions.amount) as total'))
            ->groupBy('categories.name', 'transactions.description')
            ->orderByDesc('total')
            ->get();

        return $items->map(function($item) {
            $item->category_name = $item->description ?: ($item->category_name ?: 'Sin categoría');
            return $item;
        })->groupBy('category_name')->map(fn($group) => [
            'category_name' => $group->first()->category_name,
            'total' => (float)$group->sum('total')
        ])->values()->sortByDesc('total')->values();
    }
}
