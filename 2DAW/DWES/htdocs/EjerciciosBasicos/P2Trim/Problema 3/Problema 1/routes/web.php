<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\TaskController;
/**
 * Autor: Rafael
 * Fecha: 19/04/2026
 * Versión: 1.0
 * 
 * Este archivo define las rutas para la parte web de la aplicación.
 */

use Illuminate\Support\Facades\Route;

// --- RUTAS DE LA APLICACIÓN ---

Route::get('/', function () {
    // Si entras a la web vacía, te mandamos al login
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    // El panel de control (Dashboard)
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Todas estas rutas necesitan que estés LOGUEADO (auth)
Route::middleware('auth')->group(function () {
    
    // Rutas para gestionar tu propio perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestión de Empleados y Clientes (Solo para Administradores)
    Route::middleware('admin')->group(function () {
        Route::resource('employees', EmployeeController::class)->except(['show', 'update']);
        Route::resource('clients', ClientController::class);
    });
    Route::patch('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');

    // Gestión de Cuotas y Pagos (Solo para Administradores)
    Route::middleware('admin')->group(function () {
        Route::get('fees/remittance', [FeeController::class, 'generateRemittance'])->name('fees.remittance');
        Route::post('fees/remittance', [FeeController::class, 'storeRemittance'])->name('fees.remittance.store');
        Route::resource('fees', FeeController::class);
        Route::get('fees/{fee}/invoice', [FeeController::class, 'generateInvoice'])->name('fees.invoice');
        
        // Esta es la ruta para simular el pago (Problema 4.4)
        Route::post('fees/{fee}/pay', [FeeController::class, 'pay'])->name('fees.pay');
    });

    // Gestión de Tareas de Incidencias
    Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::middleware('admin')->group(function () {
        Route::resource('tasks', TaskController::class)->except(['index', 'show', 'update']);
    });
    Route::get('tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::patch('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
});

// Ruta pública para que los clientes registren incidencias sin loguearse
Route::get('incidencias/create', [TaskController::class, 'createPublic'])->name('tasks.public.create');
Route::post('incidencias', [TaskController::class, 'storePublic'])->name('tasks.public.store');

// --- RUTAS PARA EL PROBLEMA 3 (Vistas Dinámicas) ---

// 3.1: CRUD de Clientes usando JavaScript y DataTables
Route::get('gestor-js', function() {
    return view('clients.index_js');
})->middleware(['auth', 'admin'])->name('clients.js');

// API interna para que el JavaScript pida datos
Route::get('api/clients', [ClientController::class, 'apiIndex'])->middleware('auth');
Route::post('api/clients', [ClientController::class, 'apiStore'])->middleware(['auth', 'admin']);
Route::delete('api/clients/{client}', [ClientController::class, 'apiDestroy'])->middleware(['auth', 'admin']);

// 3.2: Vista con Vue y Quasar (usando CDN)
Route::get('gestor-quasar', function() {
    return view('clients.index_quasar');
})->middleware(['auth', 'admin'])->name('clients.quasar');

// 3.3: Vista con Vue moderno (Vite + Inertia)
Route::get('gestor-vue-vite', [TaskController::class, 'indexVite'])->middleware(['auth'])->name('tasks.vite');

// --- RUTAS PARA EL PROBLEMA 4 (Servicios) ---

// 4.2: API REST para Tareas (esto lo usa Swagger)
Route::apiResource('api/tasks', \App\Http\Controllers\Api\TaskApiController::class);

// 4.3: Registro y Login con Google (Socialite)
Route::get('auth/google', [\App\Http\Controllers\Auth\SocialController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [\App\Http\Controllers\Auth\SocialController::class, 'handleGoogleCallback']);

// Cargamos las rutas de autenticación por defecto de Laravel
require __DIR__.'/auth.php';
