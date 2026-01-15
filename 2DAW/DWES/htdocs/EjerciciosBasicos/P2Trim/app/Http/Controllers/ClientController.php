<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Client;
use App\Models\Province;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index()
    {
        return view('clients.index', [
            'clients' => Client::all()
        ]);
    }

    public function create()
    {
        return view('clients.create', [
            'countries' => ['España', 'Portugal', 'Francia', 'Italia']
        ]);
    }

    public function store(Request $request)
    {
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
        return view('clients.edit', [
            'client' => $client,
            'countries' => ['España', 'Portugal', 'Francia', 'Italia']
        ]);
    }

    public function update(Request $request, Client $client)
    {
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
        $client->update(['is_active' => false]);
        return redirect()->route('clients.index')->with('success', 'Cliente dado de baja con éxito.');
    }
}
