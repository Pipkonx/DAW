<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Administrador\UserController;
use App\Http\Controllers\ControladorTransaccion;
use App\Http\Controllers\UserProfileController;

Route::get('/', function () {
    return redirect()->route('acceso');
});

Route::get('/login', function () { return redirect()->route('acceso'); })->name('login');

// Rutas de registro manual
Route::get('registro', [RegisterController::class, 'showRegistrationForm'])->name('registro');
Route::post('registro', [RegisterController::class, 'register']);

// Rutas de autenticación manual
Route::get('acceso', [LoginController::class, 'showLoginForm'])->name('acceso');
Route::post('acceso', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('registro', [RegisterController::class, 'showRegistrationForm'])->name('registro');
Route::post('registro', [RegisterController::class, 'register']);

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    Route::get('/panel', function () {
        return view('dashboard'); // La vista sigue siendo 'dashboard.blade.php' por ahora
    })->name('panel');

    // Rutas de administración de usuarios (solo para administradores)
    Route::middleware(['admin'])->prefix('administrador')->group(function () {
        Route::resource('usuarios', \App\Http\Controllers\Administrador\ControladorUsuario::class);
    });

    // Rutas para la gestión de transacciones
    Route::resource('transacciones', ControladorTransaccion::class);

    Route::get('transacciones/subcategorias', [ControladorTransaccion::class, 'getSubcategorias'])->name('transacciones.subcategorias');

    // Rutas para el perfil de usuario
    Route::get('/profile/change-password', [UserProfileController::class, 'showChangePasswordForm'])->name('password.form');
    Route::post('/profile/change-password', [UserProfileController::class, 'changePassword'])->name('password.change');

    Route::get('/profile/edit-name', [UserProfileController::class, 'showEditNameForm'])->name('name.form');
    Route::post('/profile/edit-name', [UserProfileController::class, 'updateName'])->name('name.update');
});
