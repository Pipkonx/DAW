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
        $query = Task::with(['client', 'operator', 'province']);

        if (auth()->check()) {
            if (auth()->user()->isOperator()) {
                $query->where('operator_id', auth()->id());
            }
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('client_id')) {
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
        return view('tasks.create', [
            'clients' => Client::all(),
            'operators' => User::where('role', 'operator')->get(),
            'provinces' => Province::all(),
        ]);
    }

    public function store(Request $request)
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

        return redirect()->route('tasks.index')->with('success', 'Tarea creada y asignada.');
    }

    public function show(Task $task)
    {
        if (auth()->user()->isOperator() && $task->operator_id !== auth()->id()) {
            abort(403);
        }

        return view('tasks.show', [
            'task' => $task->load(['client', 'operator', 'province'])
        ]);
    }

    public function update(Request $request, Task $task)
    {
        if (auth()->user()->isOperator()) {
            if ($task->operator_id !== auth()->id()) {
                abort(403);
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
                $path = $request->file('attachment')->store('tasks', 'private');
                $validated['attachment_path'] = $path;
            }

            $task->update($validated);
        } else {
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

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Tarea eliminada.');
    }

    public function createPublic()
    {
        return view('tasks.public_create', [
            'provinces' => Province::all()
        ]);
    }

    public function storePublic(Request $request)
    {
        $client = Client::where('cif', $request->cif)
                        ->where('phone', $request->phone)
                        ->first();

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

        Task::create($validated);

        return view('tasks.public_success');
    }
}
