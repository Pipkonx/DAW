<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Fee;
use App\Models\Client;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

/**
 * Controlador de Cuotas (Facturación)
 * 
 * Este controlador gestiona los cobros a los clientes, la generación de remesas
 * mensuales, la creación de facturas en PDF y el envío por correo electrónico.
 */
class FeeController extends Controller
{
    /**
     * Muestra la lista de todas las cuotas registradas.
     * 
     * @return View
     */
    public function index(): View
    {
        return view('fees.index', [
            'fees' => Fee::with('client')->latest()->get()
        ]);
    }

    /**
     * Muestra el formulario para añadir una cuota excepcional a un cliente.
     * 
     * @return View
     */
    public function create(): View
    {
        return view('fees.create', [
            'clients' => Client::where('is_active', true)->get()
        ]);
    }

    /**
     * Guarda una nueva cuota excepcional en la base de datos.
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'concept' => 'required|string|max:255',
            'emission_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        Fee::create($validated);

        return redirect()->route('fees.index')->with('success', 'Cuota excepcional añadida.');
    }

    /**
     * Muestra la pantalla de confirmación para generar la remesa mensual.
     * 
     * @return View
     */
    public function generateRemittance(): View
    {
        return view('fees.remittance', [
            'month' => now()->translatedFormat('F Y'),
            'client_count' => Client::where('is_active', true)->count()
        ]);
    }

    /**
     * Crea automáticamente la cuota mensual para todos los clientes activos.
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function storeRemittance(Request $request): RedirectResponse
    {
        $clients = Client::where('is_active', true)->get();
        $date = now()->toDateString();
        $concept = "Cuota mensual mantenimiento - " . now()->translatedFormat('F Y');

        foreach ($clients as $client) {
            Fee::create([
                'client_id' => $client->id,
                'concept' => $concept,
                'emission_date' => $date,
                'amount' => $client->monthly_fee,
            ]);
        }

        return redirect()->route('fees.index')->with('success', 'Remesa mensual generada con éxito.');
    }

    /**
     * Genera un archivo PDF de la factura y lo envía por correo al cliente.
     * 
     * @param Fee $fee
     * @return \Illuminate\Http\Response
     */
    public function generateInvoice(Fee $fee)
    {
        $fee->load('client');
        
        // Creamos el PDF usando la librería DomPDF (como pide el enunciado)
        $pdf = Pdf::loadView('invoices.template', ['fee' => $fee]);
        $pdfContent = $pdf->output();
        $filename = 'factura_' . $fee->id . '.pdf';
        $path = 'invoices/' . $filename;
        
        // Guardamos el PDF en el almacenamiento privado (no accesible directamente desde la web)
        Storage::disk('private')->put($path, $pdfContent);
        
        $fee->update(['invoice_path' => $path]);

        // Enviamos el correo automáticamente
        try {
            Mail::to($fee->client->email)->send(new \App\Mail\InvoiceMail($fee, $pdfContent));
            session()->flash('success', 'Factura generada y enviada correctamente.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al enviar el correo, pero el PDF se ha generado.');
        }

        return $pdf->download($filename);
    }

    /**
     * Actualiza los datos de una cuota y gestiona el pago si se marca como "S".
     * 
     * @param Request $request
     * @param Fee $fee
     * @return RedirectResponse
     */
    public function update(Request $request, Fee $fee): RedirectResponse
    {
        $validated = $request->validate([
            'concept' => 'required|string|max:255',
            'emission_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'is_paid' => 'required|in:S,N',
            'payment_date' => 'nullable|date|required_if:is_paid,S',
            'notes' => 'nullable|string',
        ]);

        // Si se marca como pagada y no lo estaba, usamos la lógica del modelo
        if ($validated['is_paid'] === 'S' && $fee->is_paid !== 'S') {
            $fee->markAsPaid();
            // Actualizamos solo los campos que NO gestiona markAsPaid()
            unset($validated['is_paid'], $validated['payment_date']);
        }

        $fee->update($validated);

        return redirect()->route('fees.index')->with('success', 'Cuota actualizada.');
    }

    /**
     * Elimina una cuota.
     * 
     * @param Fee $fee
     * @return RedirectResponse
     */
    public function destroy(Fee $fee): RedirectResponse
    {
        $fee->delete();
        return redirect()->route('fees.index')->with('success', 'Cuota eliminada.');
    }

    /**
     * Proceso simplificado de pago rápido.
     * 
     * @param Fee $fee
     * @return RedirectResponse
     */
    public function pay(Fee $fee): RedirectResponse
    {
        // La lógica compleja de API y conversión ahora está en el Modelo (markAsPaid)
        // Esto hace que esta función sea muy corta y fácil de entender.
        $fee->markAsPaid();

        return redirect()->route('fees.index')->with('success', 'Pago procesado correctamente.');
    }
}


