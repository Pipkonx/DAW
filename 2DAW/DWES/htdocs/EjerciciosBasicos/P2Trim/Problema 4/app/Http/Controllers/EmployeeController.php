<?php

/**
 * Autor: Rafael
 * Fecha: 19/04/2026
 * Versión: 1.0
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Controlador de Empleados
 * 
 * Permite a los administradores gestionar la plantilla de la empresa.
 * Se encarga de altas, bajas y modificaciones de datos de contacto.
 */
class EmployeeController extends Controller
{
    public function index()
    {
        // Lista todos los empleados (usuarios del sistema)
        return view('employees.index', [
            'employees' => User::all()
        ]);
    }

    public function create()
    {
        // Muestra el formulario para registrar un nuevo empleado
        return view('employees.create');
    }

    public function store(Request $request)
    {
        // Valida los datos del nuevo empleado
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

        // Ciframos la contraseña antes de guardarla para que sea segura
        $validated['password'] = Hash::make($validated['password']);
        
        User::create($validated);

        return redirect()->route('employees.index')->with('success', 'Empleado creado con éxito.');
    }

    public function edit(User $employee)
    {
        // Muestra el formulario para editar los datos de un empleado
        return view('employees.edit', [
            'employee' => $employee
        ]);
    }

    public function update(Request $request, User $employee)
    {
        // Comprobamos si el usuario que está navegando es Administrador
        $isAdmin = auth()->user()->isAdmin();
        
        if ($isAdmin) {
            // Si eres Admin, puedes cambiarlo casi todo
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
            // Si no eres Admin, solo puedes cambiar lo básico de ti mismo
            if (auth()->id() !== $employee->id) {
                abort(403, 'No puedes editar a otros empleados.');
            }
            $validated = $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($employee->id)],
                'hire_date' => 'required|date', // Aunque no suele cambiarse, se deja por compatibilidad
            ]);
        }

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Empleado actualizado con éxito.');
    }

    public function destroy(User $employee)
    {
        // No puedes darte de baja a ti mismo (te quedarías fuera del sistema)
        if ($employee->id === auth()->id()) {
            return back()->with('error', 'No puedes darte de baja a ti mismo.');
        }

        // Baja lógica: simplemente lo desactivamos
        $employee->update(['is_active' => false]);
        return redirect()->route('employees.index')->with('success', 'Empleado dado de baja con éxito.');
    }
}
