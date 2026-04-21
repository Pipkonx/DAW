<?php

namespace App\Services\Transaction;

use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class TransactionExportService
{
    /**
     * Generates a PDF export for the given transactions.
     */
    public function exportPdf($transactions, $user)
    {
        return Pdf::loadView('exports.transactions', [
            'transactions' => $transactions,
            'user' => $user
        ])->download('historial-transacciones.pdf');
    }

    /**
     * Generates a CSV stream for the given transactions.
     */
    public function exportCsv($transactions)
    {
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=historial-transacciones.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM for UTF-8 Excel
            fputcsv($file, ['Fecha', 'Tipo', 'Activo / Concepto', 'Cantidad', 'Precio', 'Total', 'Descripción'], ';');

            foreach ($transactions as $tx) {
                $assetName = $tx->asset ? ($tx->asset->ticker . ' - ' . $tx->asset->name) : $tx->description;
                $typeName = $this->mapTypeName($tx->type);

                fputcsv($file, [
                    $tx->date->format('d/m/Y'),
                    $typeName,
                    $assetName,
                    number_format($tx->quantity ?? 0, 8, ',', ''),
                    number_format($tx->price_per_unit ?? 0, 4, ',', ''),
                    number_format($tx->amount, 2, ',', ''),
                    $tx->description
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function mapTypeName($type)
    {
        return match($type) {
            'buy' => 'Compra',
            'sell' => 'Venta',
            'dividend' => 'Dividendo',
            'reward' => 'Recompensa',
            'gift' => 'Regalo',
            'income' => 'Ingreso',
            'expense' => 'Gasto',
            default => ucfirst($type)
        };
    }
}
