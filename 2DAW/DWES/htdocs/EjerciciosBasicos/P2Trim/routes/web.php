<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestión de Empleados
    Route::middleware('admin')->group(function () {
        Route::resource('employees', EmployeeController::class)->except(['show', 'update']);
        Route::resource('clients', ClientController::class);
    });
    Route::patch('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');

    // Gestión de Cuotas
    Route::middleware('admin')->group(function () {
        Route::get('fees/remittance', [FeeController::class, 'generateRemittance'])->name('fees.remittance');
        Route::post('fees/remittance', [FeeController::class, 'storeRemittance'])->name('fees.remittance.store');
        Route::resource('fees', FeeController::class);
        Route::get('fees/{fee}/invoice', [FeeController::class, 'generateInvoice'])->name('fees.invoice');
    });

    // Gestión de Tareas
    Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::middleware('admin')->group(function () {
        Route::resource('tasks', TaskController::class)->except(['index', 'show', 'update']);
    });
    Route::get('tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::patch('tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
});

// Ruta pública para que los clientes registren incidencias
Route::get('incidencias/create', [TaskController::class, 'createPublic'])->name('tasks.public.create');
Route::post('incidencias', [TaskController::class, 'storePublic'])->name('tasks.public.store');

// --- RUTAS PARA PROBLEMA 3 (CRUDs Dinámicos) ---

// 3.1: CRUD de Clientes usando JavaScript y DataTables (CDN)
Route::get('gestor-js', function() {
    return view('clients.index_js');
})->middleware(['auth', 'admin'])->name('clients.js');

// API sencilla para que el JS pueda leer y guardar datos sin recargar
Route::get('api/clients', [ClientController::class, 'apiIndex'])->middleware('auth');
Route::post('api/clients', [ClientController::class, 'apiStore'])->middleware(['auth', 'admin']);
Route::delete('api/clients/{client}', [ClientController::class, 'apiDestroy'])->middleware(['auth', 'admin']);

// 3.2: Vista de Vue/Quasar usando CDN (Archivo estático o Blade sencillo)
Route::get('gestor-quasar', function() {
    return view('clients.index_quasar');
})->middleware(['auth', 'admin'])->name('clients.quasar');

// 3.3: Vista de Vue usando VITE y componentes .vue (Inertia)
Route::get('gestor-vue-vite', [TaskController::class, 'indexVite'])->middleware(['auth'])->name('tasks.vite');

require __DIR__.'/auth.php';
