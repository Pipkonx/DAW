<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Services\Transaction\TransactionService;
use Illuminate\Support\Facades\Auth;

class TransactionActionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Store a new transaction.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense,buy,sell,dividend,reward,gift',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'asset_name' => 'nullable|string',
            'asset_full_name' => 'nullable|string',
            'asset_type' => 'nullable|string|in:stock,crypto,fund,etf,bond,real_estate,other',
            'market_asset_id' => 'nullable|exists:market_assets,id',
            'isin' => 'nullable|string',
            'quantity' => 'nullable|numeric|min:0',
            'price_per_unit' => 'nullable|numeric|min:0',
            'portfolio_id' => 'required_if:type,buy,sell,dividend|nullable|exists:portfolios,id',
            'currency_code' => 'nullable|string|size:3',
            'time' => 'nullable|date_format:H:i',
            'fees' => 'nullable|numeric|min:0',
            'exchange_fees' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
        ]);

        try {
            $this->transactionService->store($validated);
            return redirect()->back()->with('success', 'Transacción registrada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()]);
        }
    }

    /**
     * Update an existing transaction.
     */
    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'quantity' => 'nullable|numeric|min:0',
            'price_per_unit' => 'nullable|numeric|min:0',
            'currency_code' => 'nullable|string|size:3',
            'time' => 'nullable|date_format:H:i',
            'fees' => 'nullable|numeric|min:0',
            'exchange_fees' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
        ]);

        try {
            $this->transactionService->update($transaction, $validated);
            return redirect()->back()->with('success', 'Transacción actualizada.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a transaction.
     */
    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) abort(403);

        try {
            $this->transactionService->delete($transaction);
            return redirect()->back()->with('success', 'Transacción eliminada.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk delete transactions.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        $transactions = Transaction::whereIn('id', $request->ids)->where('user_id', Auth::id())->get();

        try {
            foreach ($transactions as $transaction) {
                $this->transactionService->delete($transaction);
            }
            return redirect()->back()->with('success', 'Transacciones eliminadas masivamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error en eliminación masiva: ' . $e->getMessage()]);
        }
    }
}
