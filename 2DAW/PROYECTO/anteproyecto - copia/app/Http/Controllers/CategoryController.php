<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id();
        $userCategoriesCount = Category::where('user_id', $userId)->count();

        if ($userCategoriesCount === 0) {
            // Clone system categories for this user
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

        // Get user categories
        $categories = Category::where('user_id', $userId)
            ->orderBy('usage_count', 'desc')
            ->orderBy('name')
            ->get();
            
        // Structure them hierarchically
        $parents = $categories->whereNull('parent_id');
        $structured = $parents->map(function ($parent) use ($categories) {
            $children = $categories->where('parent_id', $parent->id)->values();
            $parent->children = $children;
            return $parent;
        })->values();

        return Inertia::render('Categories/Index', [
            'categories' => $structured
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'parent_id' => 'nullable|exists:categories,id',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $category = Category::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'parent_id' => $validated['parent_id'],
            'icon' => $validated['icon'],
            'color' => $validated['color'],
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string',
            'color' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if has transactions
        // Ideally, we should soft delete or reassing transactions.
        // For now, let's just delete and set transactions category_id to null?
        // Or restrict deletion.
        // The migration has `onDelete('set null')`? No, let's check.
        // Migration has `foreignId('category_id')->nullable()->constrained()->onDelete('set null')`?
        // Let's check `add_category_id_to_transactions_table.php`.
        
        $category->delete();

        return redirect()->back()->with('success', 'Categoría eliminada exitosamente.');
    }

    public function toggleActive(Category $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403);
        }

        $category->update(['is_active' => !$category->is_active]);

        return redirect()->back()->with('success', 'Estado de categoría actualizado.');
    }
}
