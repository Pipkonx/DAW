<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('employees.index', [
            'employees' => User::all()
        ]);
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dni' => 'required|string|max:9|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'hire_date' => 'required|date',
            'role' => 'required|in:admin,operator',
            'password' => 'required|string|min:8',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        User::create($validated);

        return redirect()->route('employees.index')->with('success', 'Empleado creado con éxito.');
    }

    public function edit(User $employee)
    {
        return view('employees.edit', [
            'employee' => $employee
        ]);
    }

    public function update(Request $request, User $employee)
    {
        $isAdmin = auth()->user()->isAdmin();
        
        if ($isAdmin) {
            $validated = $request->validate([
                'dni' => ['required', 'string', 'max:9', Rule::unique('users')->ignore($employee->id)],
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($employee->id)],
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'hire_date' => 'required|date',
                'role' => 'required|in:admin,operator',
                'is_active' => 'required|boolean',
            ]);
        } else {
            if (auth()->id() !== $employee->id) {
                abort(403);
            }
            $validated = $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($employee->id)],
                'hire_date' => 'required|date',
            ]);
        }

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Empleado actualizado con éxito.');
    }

    public function destroy(User $employee)
    {
        if ($employee->id === auth()->id()) {
            return back()->with('error', 'No puedes darte de baja a ti mismo.');
        }

        $employee->update(['is_active' => false]);
        return redirect()->route('employees.index')->with('success', 'Empleado dado de baja con éxito.');
    }
}
