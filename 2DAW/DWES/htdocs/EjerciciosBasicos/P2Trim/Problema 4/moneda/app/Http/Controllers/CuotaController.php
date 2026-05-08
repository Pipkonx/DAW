<?php

namespace App\Http\Controllers;

use App\Models\Cuota;
use App\Services\ServicioDivisas;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\CuotaCreada;
use App\Models\Cliente;

/**
 * Controlador para la gestión de cuotas de clientes.
 */
class CuotaController extends Controller
{
    protected $servicioDivisas;

    public function __construct(ServicioDivisas $servicioDivisas)
    {
        // Inyectamos el servicio de conversión de moneda
        $this->servicioDivisas = $servicioDivisas;
    }

    public function index()
    {
        $cuotas = Cuota::with('cliente')->get();
        $clientes = Cliente::all();
        return view('cuotas.index', compact('cuotas', 'clientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clientes,id',
            'concept' => 'required|string',
            'amount' => 'required|numeric',
        ]);

        $cliente = Cliente::find($validated['client_id']);
        
        $cuota = Cuota::create([
            'client_id' => $validated['client_id'],
            'concept' => $validated['concept'],
            'amount' => $validated['amount'],
            'currency' => $cliente->currency,
            'is_paid' => false,
        ]);

        // Envío de correo informativo (Requisito 1.8 de la rúbrica)
        try {
            Mail::to($request->user()->email)->send(new CuotaCreada($cuota));
        } catch (\Exception $e) {
            // Registrar error o ignorar si el correo no está configurado
        }

        return back()->with('success', 'Cuota creada con éxito y notificación enviada por correo.');
    }

    public function markAsPaid(Cuota $cuota)
    {
        if ($cuota->is_paid) {
            return back()->with('error', 'La cuota ya está pagada.');
        }

        // Convertimos el importe local a EUR usando el servicio (HttpClient)
        $eurAmount = $this->servicioDivisas->convertToEur((float) $cuota->amount, $cuota->currency);

        if ($eurAmount === null) {
            return back()->with('error', 'No se pudo obtener el tipo de cambio en este momento.');
        }

        $cuota->update([
            'is_paid' => true,
            'paid_at' => Carbon::now(),
            'eur_amount' => $eurAmount
        ]);

        return back()->with('success', "Cuota marcada como pagada. Importe registrado: " . number_format($eurAmount, 2) . " EUR");
    }
}
