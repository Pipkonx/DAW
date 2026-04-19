<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| EXPLICACIÓN: "use App\Models\..."
|--------------------------------------------------------------------------
| Estas líneas de "use" son como decirle a PHP: "Oye, voy a necesitar 
| usar los planos de las Cuotas (Fee) y de los Clientes (Client)". 
| Es como traer las herramientas necesarias a tu mesa de trabajo.
*/
use App\Models\Fee;
use App\Models\Client;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class FeeController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | EXPLICACIÓN: Fee::with('client')->latest()->get()
        |--------------------------------------------------------------------------
        | Esta línea es una "cadena" de órdenes para la base de datos:
        | 1. Fee:: -> "Empezamos con las cuotas..."
        | 2. with('client') -> "...y tráete también los datos del cliente de cada cuota (para no ir uno a uno luego)."
        | 3. latest() -> "...ordénalas de la más nueva a la más vieja..."
        | 4. get() -> "...¡y ahora tráemelas todas!"
        */
        return view('fees.index', [
            'fees' => Fee::with('client')->latest()->get()
        ]);
    }

    public function create()
    {
        // Mostramos el formulario para crear una cuota nueva
        return view('fees.create', [
            'clients' => Client::where('is_active', true)->get() // Solo clientes que estén "activos"
        ]);
    }

    public function store(Request $request)
    {
        // Validamos que los datos que vienen del formulario sean correctos
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'concept' => 'required|string|max:255',
            'emission_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Creamos la cuota en la base de datos
        Fee::create($validated);

        return redirect()->route('fees.index')->with('success', 'Cuota excepcional añadida.');
    }

    public function generateRemittance()
    {
        // Vista para confirmar la generación de cuotas mensuales para todos
        return view('fees.remittance', [
            'month' => now()->format('F Y'),
            'client_count' => Client::where('is_active', true)->count()
        ]);
    }

    public function storeRemittance(Request $request)
    {
        // Generamos la cuota mensual para cada cliente activo
        $clients = Client::where('is_active', true)->get();
        $date = now()->toDateString();
        $concept = "Cuota mensual mantenimiento - " . now()->format('F Y');

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

    public function generateInvoice(Fee $fee)
    {
        // Generamos un PDF de la factura
        $fee->load('client');
        
        $pdf = Pdf::loadView('invoices.template', ['fee' => $fee]);
        $pdfContent = $pdf->output();
        $filename = 'factura_' . $fee->id . '.pdf';
        $path = 'invoices/' . $filename;
        
        // Guardamos el PDF en una carpeta privada
        \Storage::disk('private')->put($path, $pdfContent);
        
        $fee->update(['invoice_path' => $path]);

        // Intentamos enviar el correo al cliente con la factura adjunta
        try {
            \Mail::to($fee->client->email)->send(new \App\Mail\InvoiceMail($fee, $pdfContent));
            $message = 'Factura generada y enviada por correo con éxito.';
        } catch (\Exception $e) {
            $message = 'Factura generada pero hubo un error al enviar el correo.';
        }

        return $pdf->download($filename);
    }

    public function update(Request $request, Fee $fee)
    {
        // Actualizamos una cuota existente
        $validated = $request->validate([
            'concept' => 'required|string|max:255',
            'emission_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'is_paid' => 'required|in:S,N',
            'payment_date' => 'nullable|date|required_if:is_paid,S',
            'notes' => 'nullable|string',
        ]);

        $fee->load('client');

        // Si el usuario marca la cuota como pagada ahora:
        if ($validated['is_paid'] === 'S' && $fee->is_paid !== 'S') {
            $currency = strtolower($fee->client->currency); // p.ej. 'usd'
            
            // Consultamos cuánto vale la moneda del cliente en Euros
            try {
                $response = Http::get("https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/{$currency}.json");
                
                if ($response->successful()) {
                    $rates = $response->json();
                    $rate = $rates[$currency]['eur'] ?? 1; // El valor del cambio
                    
                    $validated['exchange_rate'] = $rate;
                    $validated['amount_eur'] = $fee->amount * $rate;
                }
            } catch (\Exception $e) {
                // Si falla internet, ponemos lo mismo que en la moneda original
                $validated['amount_eur'] = $fee->amount;
            }
        }

        $fee->update($validated);

        return redirect()->route('fees.index')->with('success', 'Cuota actualizada.');
    }

    public function destroy(Fee $fee)
    {
        // Borramos la cuota
        $fee->delete();
        return redirect()->route('fees.index')->with('success', 'Cuota eliminada.');
    }

    public function pay(Fee $fee)
    {
        // Simulación de un pago con tarjeta o PayPal
        sleep(1); 

        $fee->load('client');
        $currency = strtolower($fee->client->currency);
        $amount_eur = $fee->amount;
        $exchange_rate = 1;

        // Buscamos el precio del Euro para esta moneda
        try {
            $response = Http::get("https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/{$currency}.json");
            if ($response->successful()) {
                $rates = $response->json();
                $exchange_rate = $rates[$currency]['eur'] ?? 1;
                $amount_eur = $fee->amount * $exchange_rate;
            }
        } catch (\Exception $e) {
            // Error ignorado por sencillez
        }

        // Marcamos como pagado y guardamos cuándo y a qué precio estaba el Euro
        $fee->update([
            'is_paid' => 'S',
            'payment_date' => now()->toDateString(),
            'exchange_rate' => $exchange_rate,
            'amount_eur' => $amount_eur,
            'notes' => $fee->notes . "\n[Pago realizado automáticamente]"
        ]);

        return redirect()->route('fees.index')->with('success', 'Pago realizado con éxito.');
    }
}
