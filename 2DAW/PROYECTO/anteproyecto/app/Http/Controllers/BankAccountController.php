<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BankAccountController extends Controller
{
    public function index()
    {
        $accounts = Auth::user()->bankAccounts;
        
        // Calculate total liquid assets
        $totalLiquid = $accounts->sum('balance');
        
        // Return view for Projections/Financial Planning
        return Inertia::render('FinancialPlanning/Index', [
            'accounts' => $accounts,
            'totalLiquid' => $totalLiquid,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:checking,savings',
            'balance' => 'required|numeric|min:0',
            'apy' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
        ]);

        Auth::user()->bankAccounts()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'balance' => $validated['balance'],
            'apy' => $validated['apy'] ?? 0,
            'currency' => $validated['currency'] ?? 'EUR',
        ]);

        return redirect()->back()->with('success', 'Cuenta añadida exitosamente.');
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        if ($bankAccount->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:checking,savings',
            'balance' => 'required|numeric|min:0',
            'apy' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
        ]);

        $bankAccount->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'balance' => $validated['balance'],
            'apy' => $validated['apy'] ?? 0,
            'currency' => $validated['currency'] ?? 'EUR',
        ]);

        return redirect()->back()->with('success', 'Cuenta actualizada exitosamente.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        if ($bankAccount->user_id !== Auth::id()) {
            abort(403);
        }

        $bankAccount->delete();

        return redirect()->back()->with('success', 'Cuenta eliminada exitosamente.');
    }
}
