<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Services\Transaction\TransactionExportService;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Auth;

class TransactionExportController extends Controller
{
    protected $exportService;
    protected $transactionController;

    public function __construct(TransactionExportService $exportService, TransactionController $transactionController)
    {
        $this->exportService = $exportService;
        $this->transactionController = $transactionController;
    }

    /**
     * Export transaction history.
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $format = $request->input('format', 'csv');
        $portfolioId = $request->input('portfolio_id', 'aggregated');
        $assetId = $request->input('asset_id');

        // Reconstruct assetIds for filtering
        $assetsQuery = Asset::where('user_id', $user->id);
        if ($portfolioId !== 'aggregated') $assetsQuery->where('portfolio_id', $portfolioId);
        $assetIds = $assetsQuery->pluck('id');

        // Use the query logic from TransactionController
        $query = $this->transactionController->getTransactionsQuery($user, $portfolioId, $assetId, $assetIds);

        if ($request->filled('start_date')) $query->whereDate('date', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('date', '<=', $request->end_date);

        $transactions = $query->get();

        if ($format === 'pdf') {
            return $this->exportService->exportPdf($transactions, $user);
        }

        return $this->exportService->exportCsv($transactions);
    }
}
