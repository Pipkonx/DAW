<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Task;
use App\Models\Client;
use App\Models\User;
use App\Models\Province;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        // Cogemos el usuario que ha entrado a la web
        $user = auth()->user();
        
        // Empezamos la consulta trayendo también los datos de cliente, operario y provincia (para que la web cargue rápido)
        $query = Task::with(['client', 'operator', 'province']);

        // Si es un operario, solo le enseñamos las tareas que él tenga que hacer
        if ($user && $user->role === 'operator') {
            $query->where('operator_id', $user->id);
        }

        // Filtramos por estado si el usuario lo pide en la web
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtramos por cliente si el usuario lo pide
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        return view('tasks.index', [
            'tasks' => $query->latest()->get(),
            'clients' => Client::all(),
            'operators' => User::where('role', 'operator')->get(),
        ]);
    }

    public function create()
    {
        // Muestra el formulario para crear una tarea nueva asignando cliente y operario
        return view('tasks.create', [
            'clients' => Client::all(),
            'operators' => User::where('role', 'operator')->get(),
            'provinces' => Province::all(),
        ]);
    }

    public function store(Request $request)
    {
        // Validamos que todos los campos del formulario sean correctos
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'contact_person' => 'required|string|max:255',
            'contact_phone' => 'required|string|regex:/^[0-9\-\+\s]{9,15}$/',
            'contact_email' => 'required|email|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|size:5',
            'province_code' => 'required|exists:provinces,code',
            'operator_id' => 'required|exists:users,id',
            'previous_notes' => 'nullable|string',
        ]);

        // Guardamos la tarea en la base de datos
        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Tarea creada y asignada al operario correctamente.');
    }

    public function show(Task $task)
    {
        // Muestra el detalle de una tarea concreta
        return view('tasks.show', [
            'task' => $task->load(['client', 'operator', 'province'])
        ]);
    }

    public function update(Request $request, Task $task)
    {
        // Si el usuario es un operario, solo puede cambiar ciertas cosas
        if (auth()->user()->isOperator()) {
            if ($task->operator_id !== auth()->id()) {
                abort(403, 'No puedes editar una tarea que no tienes asignada.');
            }

            $validated = $request->validate([
                'status' => 'required|in:pending,done,cancelled',
                'posterior_notes' => 'nullable|string',
                'completion_date' => 'nullable|date|after_or_equal:today',
                'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            ]);

            // Si sube un archivo (foto o documento), lo guardamos
            if ($request->hasFile('attachment')) {
                // Si ya había uno, lo borramos antes de poner el nuevo
                if ($task->attachment_path) {
                    Storage::disk('private')->delete($task->attachment_path);
                }
                $path = $request->file('attachment')->store('tasks', 'private');
                $validated['attachment_path'] = $path;
            }

            $task->update($validated);
        } else {
            // Si eres Admin, puedes cambiar cualquier dato de la tarea
            $validated = $request->validate([
                'client_id' => 'required|exists:clients,id',
                'contact_person' => 'required|string|max:255',
                'contact_phone' => 'required|string|regex:/^[0-9\-\+\s]{9,15}$/',
                'contact_email' => 'required|email|max:255',
                'description' => 'required|string',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'postal_code' => 'required|string|size:5',
                'province_code' => 'required|exists:provinces,code',
                'operator_id' => 'required|exists:users,id',
                'status' => 'required|in:pending,done,cancelled',
                'completion_date' => 'nullable|date',
                'previous_notes' => 'nullable|string',
                'posterior_notes' => 'nullable|string',
            ]);

            $task->update($validated);
        }

        return redirect()->route('tasks.index')->with('success', 'Tarea actualizada con éxito.');
    }

    public function destroy(Task $task)
    {
        // Borra la tarea
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Tarea eliminada.');
    }

    public function createPublic()
    {
        // Formulario público para que un cliente nos envíe una incidencia sin registrarse
        return view('tasks.public_create', [
            'provinces' => Province::all()
        ]);
    }

    public function storePublic(Request $request)
    {
        // Buscamos si el cliente existe por su CIF y su teléfono
        $client = Client::where('cif', $request->cif)
                        ->where('phone', $request->phone)
                        ->first();

        // Si no lo encontramos, avisamos de que los datos no son correctos
        if (!$client) {
            return back()->withErrors(['cif' => 'Los datos de CIF y teléfono no coinciden con nuestros registros.']);
        }

        $validated = $request->validate([
            'contact_person' => 'required|string|max:255',
            'contact_phone' => 'required|string|regex:/^[0-9\-\+\s]{9,15}$/',
            'contact_email' => 'required|email|max:255',
            'description' => 'required|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|size:5',
            'province_code' => 'required|exists:provinces,code',
        ]);

        $validated['client_id'] = $client->id;
        $validated['status'] = 'pending';

        // Creamos la tarea que el cliente ha enviado desde la zona pública
        Task::create($validated);

        return view('tasks.public_success');
    }

    // --- MÉTODOS PARA EL PROBLEMA 3.3 (Vistas con Vue moderno) ---
    public function indexVite()
    {
        // Envía los datos a un componente de Vue 3 a través de Inertia
        return Inertia::render('Tasks/Index', [
            'tasks' => Task::with('client', 'operator')->latest()->get(),
            'operators' => User::where('role', 'operator')->get(),
        ]);
    }
}
