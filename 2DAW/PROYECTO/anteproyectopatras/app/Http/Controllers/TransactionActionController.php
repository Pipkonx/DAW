<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use App\Services\Transaction\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionActionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Registrar una nueva transacción manualmente.
     */
    public function store(StoreTransactionRequest $request)
    {
        try {
            $this->transactionService->store($request->validated());
            $msg = $this->generateSuccessMessage($request->validated(), 'creada');
            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error al guardar: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualizar una transacción existente.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) abort(403);

        try {
            $this->transactionService->update($transaction, $request->validated());
            $msg = $this->generateSuccessMessage(array_merge($transaction->toArray(), $request->validated()), 'actualizada');
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
     * Generar un mensaje de éxito contextualizado según el tipo de operación.
     */
    private function generateSuccessMessage(array $data, string $action)
    {
        $amount = number_format($data['amount'] ?? 0, 2, ',', '.') . '€';
        $type = $data['type'] ?? '';
        
        switch ($type) {
            case 'expense':
                $cat = $data['category_name'] ?? 'Gastos';
                return $action === 'creada' ? "Has registrado un gasto de {$amount} en {$cat}." : "Gasto de {$amount} en {$cat} actualizado.";
            case 'income':
                $cat = $data['category_name'] ?? 'Ingresos';
                return $action === 'creada' ? "Has registrado un ingreso de {$amount} en {$cat}." : "Ingreso de {$amount} en {$cat} actualizado.";
            case 'buy':
            case 'sell':
                $verb = ($type === 'buy') ? 'comprado' : 'vendido';
                $asset = $data['asset_name'] ?? 'activo';
                return $action === 'creada' ? "Has {$verb} " . ($data['quantity'] ?? 0) . " uds de {$asset} por {$amount}." : ucfirst($type) . " de {$asset} actualizada ({$amount}).";
            case 'dividend':
                return $action === 'creada' ? "Has registrado {$amount} en dividendos." : "Dividendo actualizado ({$amount}).";
            default:
                return "Transacción {$action} correctamente ({$amount}).";
        }
    }
}
