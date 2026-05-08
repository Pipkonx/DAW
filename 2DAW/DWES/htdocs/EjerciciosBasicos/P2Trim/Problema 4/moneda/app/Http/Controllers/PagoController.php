<?php

namespace App\Http\Controllers;

use App\Models\Cuota;
use App\Services\ServicioDivisas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PagoController extends Controller
{
    protected $servicioDivisas;

    public function __construct(ServicioDivisas $servicioDivisas)
    {
        $this->servicioDivisas = $servicioDivisas;
    }

    /**
     * Simula la redirección a PayPal
     */
    public function pagarConPaypal(Cuota $cuota)
    {
        if ($cuota->is_paid) {
            return redirect()->route('cuotas.index')->with('error', 'Esta cuota ya ha sido pagada.');
        }

        // En un entorno real, aquí usaríamos el SDK de PayPal para crear una orden
        // y redirigir al usuario a la URL de aprobación de PayPal.
        
        // Simulamos el proceso guardando el ID en la sesión para el callback
        session(['pending_payment_fee_id' => $cuota->id]);

        return view('pagos.simulacion_paypal', compact('cuota'));
    }

    /**
     * Maneja el retorno de la pasarela tras el pago exitoso
     */
    public function exito(Request $request)
    {
        $cuotaId = session('pending_payment_fee_id');
        
        if (!$cuotaId) {
            return redirect()->route('cuotas.index')->with('error', 'No hay ningún pago pendiente.');
        }

        $cuota = Cuota::findOrFail($cuotaId);

        if (!$cuota->is_paid) {
            // Obtenemos el tipo de cambio al momento del pago
            $eurAmount = $this->servicioDivisas->convertToEur((float) $cuota->amount, $cuota->currency);

            $cuota->update([
                'is_paid' => true,
                'paid_at' => Carbon::now(),
                'eur_amount' => $eurAmount ?? 0, // Fallback por si la API falla
            ]);
        }

        session()->forget('pending_payment_fee_id');

        return redirect()->route('cuotas.index')->with('success', '¡Pago completado con éxito mediante PayPal!');
    }
}
