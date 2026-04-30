<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Client;
use App\Models\User;
use App\Models\Province;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Inertia\Response as InertiaResponse;

/**
 * Controlador de Tareas (Incidencias)
 * 
 * Gestiona todas las operaciones relacionadas con las tareas: listado, creación,
 * edición y eliminación. Controla los permisos según si el usuario es Admin u Operario.
 */
class TaskController extends Controller
{
    /**
     * Muestra la lista de tareas filtrada.
     * 
     * @param Request $request Contiene los filtros de búsqueda (estado, cliente).
     * @return View
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        
        // Preparamos los filtros. Si es operario, solo verá lo suyo.
        $filters = $request->only(['status', 'client_id']);
        if ($user && $user->isOperator()) {
            $filters['operator_id'] = $user->id;
        }

        // Usamos el "Scope" del modelo Task para filtrar (cumple la norma de NO lógica en controlador)
        $tasks = Task::with(['client', 'operator', 'province'])
                    ->filter($filters)
                    ->latest()
                    ->get();

        return view('tasks.index', [
            'tasks' => $tasks,
            'clients' => Client::all(),
            'operators' => User::where('role', 'operator')->get(),
        ]);
    }

    /**
     * Muestra el formulario para crear una nueva tarea (Solo para Administradores).
     * 
     * @return View
     */
    public function create(): View
    {
        return view('tasks.create', [
            'clients' => Client::all(),
            'operators' => User::where('role', 'operator')->get(),
            'provinces' => Province::all(),
        ]);
    }

    /**
     * Guarda una nueva tarea en la base de datos.
     * 
     * @param Request $request Datos del formulario.
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
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

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Tarea creada con éxito.');
    }

    /**
     * Muestra los detalles de una tarea específica.
     * 
     * @param Task $task
     * @return View
     */
    public function show(Task $task): View
    {
        return view('tasks.show', [
            'task' => $task->load(['client', 'operator', 'province'])
        ]);
    }

    /**
     * Actualiza los datos de una tarea.
     * 
     * @param Request $request
     * @param Task $task
     * @return RedirectResponse
     */
    public function update(Request $request, Task $task): RedirectResponse
    {
        $user = auth()->user();

        // Lógica para Operarios: Solo pueden completar su tarea y subir archivos
        if ($user->isOperator()) {
            if ($task->operator_id !== $user->id) {
                abort(403, 'No tienes permiso sobre esta tarea.');
            }

            $validated = $request->validate([
                'status' => 'required|in:pending,done,cancelled',
                'posterior_notes' => 'nullable|string',
                'completion_date' => 'nullable|date|after_or_equal:today',
                'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:2048',
            ]);

            if ($request->hasFile('attachment')) {
                if ($task->attachment_path) {
                    Storage::disk('private')->delete($task->attachment_path);
                }
                $validated['attachment_path'] = $request->file('attachment')->store('tasks', 'private');
            }

            $task->update($validated);
        } else {
            // Lógica para Administradores: Pueden cambiarlo todo
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

        return redirect()->route('tasks.index')->with('success', 'Tarea actualizada.');
    }

    /**
     * Elimina una tarea de la base de datos (Confirmado).
     * 
     * @param Task $task
     * @return RedirectResponse
     */
    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Tarea eliminada.');
    }

    /**
     * Formulario público para que un cliente registre una incidencia.
     * 
     * @return View
     */
    public function createPublic(): View
    {
        return view('tasks.public_create', [
            'provinces' => Province::all()
        ]);
    }

    /**
     * Procesa el registro de una incidencia por parte de un cliente no logueado.
     * 
     * @param Request $request
     * @return RedirectResponse|View
     */
    public function storePublic(Request $request)
    {
        // Verificamos al cliente usando el método del modelo (cumple norma de NO lógica en controlador)
        $client = Client::findVerified($request->cif, $request->phone);

        if (!$client) {
            return back()->withErrors(['cif' => 'Los datos de CIF y teléfono no coinciden.']);
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

        Task::create($validated);

        return view('tasks.public_success');
    }

    /**
     * Muestra la lista de tareas usando la tecnología moderna de Inertia + Vue (Problema 3.3).
     * 
     * @return InertiaResponse
     */
    public function indexVite(): InertiaResponse
    {
        return Inertia::render('Tasks/Index', [
            'tasks' => Task::with('client', 'operator')->latest()->get(),
            'operators' => User::where('role', 'operator')->get(),
        ]);
    }
}


