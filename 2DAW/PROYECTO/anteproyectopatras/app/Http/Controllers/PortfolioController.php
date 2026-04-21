<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portfolio;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use App\Services\Asset\AssetService;
use App\Services\Financial\ImportService;
use Carbon\Carbon;

class PortfolioController extends Controller
{
    protected $assetService;
    protected $importService;

    public function __construct(AssetService $assetService, ImportService $importService)
    {
        $this->assetService = $assetService;
        $this->importService = $importService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'transactions' => 'nullable|array',
            'transactions.*.date' => 'required_with:transactions|date',
            'transactions.*.type' => 'required_with:transactions|string',
            'transactions.*.ticker' => 'required_with:transactions|string',
            'transactions.*.isin' => 'nullable|string',
            'transactions.*.asset_type' => 'nullable|string|in:stock,fund,etf,crypto,bond',
            'transactions.*.quantity' => 'required_with:transactions|numeric',
            'transactions.*.price_per_unit' => 'required_with:transactions|numeric',
            'transactions.*.amount' => 'required_with:transactions|numeric',
            'transactions.*.name' => 'nullable|string',
        ]);

        $portfolio = Portfolio::firstOrCreate([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
        ]);

        if (!empty($validated['transactions'])) {
            foreach ($validated['transactions'] as $txData) {
                // Use AssetService to handle complex asset finding/creation/linking
                $asset = $this->assetService->findOrCreateAndLink(Auth::id(), [
                    'portfolio_id' => $portfolio->id,
                    'ticker' => $txData['ticker'],
                    'isin' => $txData['isin'] ?? null,
                    'type' => $txData['asset_type'] ?? 'stock',
                    'name' => $txData['name'] ?? null,
                    'price_per_unit' => $txData['price_per_unit'],
                    'market_asset_id' => $txData['market_asset_id'] ?? null,
                ]);

                // Check for duplicate transaction
                $exists = Transaction::where('user_id', Auth::id())
                    ->where('asset_id', $asset->id)
                    ->where('type', strtolower($txData['type']))
                    ->whereDate('date', Carbon::parse($txData['date']))
                    ->where('quantity', $txData['quantity'])
                    ->where('amount', $txData['amount'])
                    ->exists();

                if ($exists) continue;

                // Create Transaction
                Transaction::create([
                    'user_id' => Auth::id(),
                    'asset_id' => $asset->id,
                    'type' => strtolower($txData['type']),
                    'date' => Carbon::parse($txData['date']),
                    'quantity' => $txData['quantity'],
                    'price_per_unit' => $txData['price_per_unit'],
                    'amount' => $txData['amount'],
                    'description' => 'Importación automática' . (isset($txData['original_text']) ? ' | OCR' : ''),
                    'portfolio_id' => $portfolio->id,
                ]);

                $asset->recalculateMetrics();
            }
        }

        return redirect()->back()->with('success', 'Cartera creada exitosamente.');
    }

    /**
     * Preview import from file.
     */
    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,pdf,jpg,jpeg,png|max:10240',
        ]);

        $rawTransactions = $this->importService->previewFromFile($request->file('file'));
        $transactions = [];

        // Finalize (link) each transaction for the preview
        foreach ($rawTransactions as $tx) {
            $transactions[] = $this->importService->finalizeTransaction($tx, Auth::id());
        }

        return response()->json([
            'transactions' => $transactions,
            'count' => count($transactions)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Portfolio $portfolio)
    {
        if ($portfolio->user_id !== Auth::id()) abort(403);

        $validated = $request->validate(['name' => 'required|string|max:255']);
        $portfolio->update(['name' => $validated['name']]);

        return redirect()->back()->with('success', 'Cartera actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Portfolio $portfolio)
    {
        if ($portfolio->user_id !== Auth::id()) abort(403);

        $portfolio->delete();

        return redirect()->back()->with('success', 'Cartera eliminada exitosamente.');
    }
}
