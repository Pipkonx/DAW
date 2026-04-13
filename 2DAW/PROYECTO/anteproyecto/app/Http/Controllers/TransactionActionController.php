<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Services\Transaction\TransactionService;
use App\Services\Financial\ImportService;
use Illuminate\Support\Facades\Auth;

class TransactionActionController extends Controller
{
    protected $transactionService;
    protected $importService;

    public function __construct(TransactionService $transactionService, ImportService $importService)
    {
        $this->transactionService = $transactionService;
        $this->importService = $importService;
    }

    /**
     * Registrar una nueva transacción manualmente.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense,buy,sell,dividend,reward,gift',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'category_name' => 'nullable|string|max:255',
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
            $msg = $this->generateSuccessMessage($validated, 'creada');
            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualizar una transacción existente.
     */
    public function update(Request $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) abort(403);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'category_name' => 'nullable|string|max:255',
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
            $msg = $this->generateSuccessMessage(array_merge($transaction->toArray(), $validated), 'actualizada');
            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()]);
        }
    }

    /**
     * Eliminar una transacción.
     */
    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) abort(403);

        try {
            $this->transactionService->delete($transaction);
            return redirect()->back()->with('success', 'Transacción eliminada correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    /**
     * Eliminación masiva de transacciones.
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        $transactions = Transaction::whereIn('id', $ids)->where('user_id', Auth::id())->get();

        try {
            /** @var Transaction $transaction */
            foreach ($transactions as $transaction) {
                $this->transactionService->delete($transaction);
            }
            return redirect()->back()->with('success', 'Transacciones eliminadas masivamente.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error en eliminación masiva: ' . $e->getMessage()]);
        }
    }

    /**
     * Importar transacciones desde un archivo externo (CSV/PDF).
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        try {
            $preview = $this->importService->previewFromFile($request->file('file'));
            
            if (empty($preview)) {
                return redirect()->back()->withErrors(['error' => 'No se encontraron registros válidos en el archivo.']);
            }

            $count = 0;
            foreach ($preview as $tx) {
                $this->transactionService->store([
                    'type' => $tx['type'] ?? 'expense',
                    'amount' => abs($tx['amount'] ?? 0),
                    'date' => $tx['date'] ?? now()->format('Y-m-d'),
                    'asset_name' => $tx['ticker'] ?? 'Importación',
                    'description' => 'Importado vía asistente externo',
                    'quantity' => $tx['quantity'] ?? null,
                    'price_per_unit' => $tx['price_per_unit'] ?? null,
                ]);
                $count++;
            }

            return redirect()->back()->with('success', "¡Éxito! Se han importado {$count} transacciones a tu cartera.");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error crítico al procesar archivo: ' . $e->getMessage()]);
        }
    }

    /**
     * Generar un mensaje de éxito contextualizado según el tipo de operación.
     */
    private function generateSuccessMessage(array $data, string $action)
    {
        $amount = number_format($data['amount'] ?? 0, 2, ',', '.') . '€';
        
        switch ($data['type'] ?? '') {
            case 'expense':
                $cat = $data['category_name'] ?? 'Gastos';
                return $action === 'creada' 
                    ? "Has registrado un gasto de {$amount} en {$cat}."
                    : "Gasto de {$amount} en {$cat} actualizado.";
            case 'income':
                $cat = $data['category_name'] ?? 'Ingresos';
                return $action === 'creada'
                    ? "Has registrado un ingreso de {$amount} en {$cat}."
                    : "Ingreso de {$amount} en {$cat} actualizado.";
            case 'buy':
                $qty = $data['quantity'] ?? 0;
                $asset = $data['asset_name'] ?? 'activo';
                return $action === 'creada'
                    ? "Has comprado {$qty} uds de {$asset} por {$amount}."
                    : "Compra de {$asset} actualizada ({$amount}).";
            case 'sell':
                $qty = $data['quantity'] ?? 0;
                $asset = $data['asset_name'] ?? 'activo';
                return $action === 'creada'
                    ? "Has vendido {$qty} uds de {$asset} por {$amount}."
                    : "Venta de {$asset} actualizada ({$amount}).";
            case 'dividend':
                $asset = $data['asset_name'] ?? 'tus inversiones';
                return $action === 'creada'
                    ? "Has registrado {$amount} en dividendos de {$asset}."
                    : "Dividendo de {$asset} actualizado ({$amount}).";
            case 'reward':
                $qty = $data['quantity'] ?? 0;
                $asset = $data['asset_name'] ?? 'activo';
                return $action === 'creada'
                    ? "Has recibido una recompensa de {$qty} uds de {$asset}."
                    : "Recompensa de {$asset} actualizada.";
            default:
                return "Transacción {$action} correctamente ({$amount}).";
        }
    }
}
