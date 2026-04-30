<?php


namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Province;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

/**
 * Controlador de Clientes
 * 
 * Gestiona el alta y mantenimiento de los clientes de la empresa.
 * Incluye métodos para la API usada en el Problema 3.1.
 */
class ClientController extends Controller
{
    // --- MÉTODOS PARA EL PROBLEMA 3.1 (Uso con JavaScript/DataTables) ---
    
    public function apiIndex()
    {
        // Devuelve todos los clientes en formato JSON para que el JS los lea
        return response()->json(Client::all());
    }

    public function apiStore(Request $request)
    {
        // Guarda un cliente nuevo enviado por la API de JS
        $validated = $request->validate([
            'cif' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
            'email' => 'required|email',
            'monthly_fee' => 'required|numeric',
        ]);

        $client = Client::create($validated);
        return response()->json(['success' => true, 'client' => $client]);
    }

    public function apiDestroy(Client $client)
    {
        // Borra un cliente cuando se pulsa el botón en la tabla de JS
        $client->delete();
        return response()->json(['success' => true]);
    }

    // --- MÉTODOS CRUD ESTÁNDAR (Páginas Web normales) ---

    public function index()
    {
        // Lista todos los clientes en la vista normal de Blade
        return view('clients.index', [
            'clients' => Client::all()
        ]);
    }

    public function create()
    {
        // Formulario para crear un cliente nuevo
        return view('clients.create', [
            'countries' => ['España', 'Portugal', 'Francia', 'Italia']
        ]);
    }

    public function store(Request $request)
    {
        // Valida y guarda el cliente desde el formulario web
        $validated = $request->validate([
            'cif' => 'required|string|max:20|unique:clients',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255',
            'bank_account' => 'required|string|max:255',
            'country' => 'required|string',
            'currency' => 'required|string|max:3',
            'monthly_fee' => 'required|numeric|min:0',
        ]);

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Cliente creado con éxito.');
    }

    public function edit(Client $client)
    {
        // Carga el formulario de edición con los datos del cliente
        return view('clients.edit', [
            'client' => $client,
            'countries' => ['España', 'Portugal', 'Francia', 'Italia']
        ]);
    }

    public function update(Request $request, Client $client)
    {
        // Actualiza los datos del cliente en la base de datos
        $validated = $request->validate([
            'cif' => ['required', 'string', 'max:20', Rule::unique('clients')->ignore($client->id)],
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|string|email|max:255',
            'bank_account' => 'required|string|max:255',
            'country' => 'required|string',
            'currency' => 'required|string|max:3',
            'monthly_fee' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Cliente actualizado con éxito.');
    }

    public function destroy(Client $client)
    {
        // En lugar de borrarlo, lo marcamos como "no activo" (Baja lógica)
        $client->update(['is_active' => false]);
        return redirect()->route('clients.index')->with('success', 'Cliente dado de baja con éxito.');
    }
}
