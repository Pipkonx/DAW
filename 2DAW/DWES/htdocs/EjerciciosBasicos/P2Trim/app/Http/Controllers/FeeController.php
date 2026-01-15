<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Fee;
use App\Models\Client;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class FeeController extends Controller
{
    public function index()
    {
        return view('fees.index', [
            'fees' => Fee::with('client')->latest()->get()
        ]);
    }

    public function create()
    {
        return view('fees.create', [
            'clients' => Client::where('is_active', true)->get()
        ]);
    }

    public function store(Request $request)
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

    public function generateRemittance()
    {
        return view('fees.remittance', [
            'month' => now()->format('F Y'),
            'client_count' => Client::where('is_active', true)->count()
        ]);
    }

    public function storeRemittance(Request $request)
    {
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
        $fee->load('client');
        
        $pdf = Pdf::loadView('invoices.template', ['fee' => $fee]);
        $pdfContent = $pdf->output();
        $filename = 'factura_' . $fee->id . '.pdf';
        $path = 'invoices/' . $filename;
        
        \Storage::disk('private')->put($path, $pdfContent);
        
        $fee->update(['invoice_path' => $path]);

        // Enviar correo al cliente
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
        $validated = $request->validate([
            'concept' => 'required|string|max:255',
            'emission_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'is_paid' => 'required|in:S,N',
            'payment_date' => 'nullable|date|required_if:is_paid,S',
            'notes' => 'nullable|string',
        ]);

        $fee->update($validated);

        return redirect()->route('fees.index')->with('success', 'Cuota actualizada.');
    }

    public function destroy(Fee $fee)
    {
        $fee->delete();
        return redirect()->route('fees.index')->with('success', 'Cuota eliminada.');
    }
}
