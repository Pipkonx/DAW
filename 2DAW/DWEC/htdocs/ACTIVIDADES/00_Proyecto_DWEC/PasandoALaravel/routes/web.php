<?php

use App\Http\Controllers\FinanzasController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UsuarioController::class, 'index'])->name('login');
Route::get('/registro', [UsuarioController::class, 'showRegistro'])->name('registro');

Route::post('/api/register', [UsuarioController::class, 'register']);
Route::post('/api/login', [UsuarioController::class, 'login']);
Route::post('/api/logout', [UsuarioController::class, 'logout']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [FinanzasController::class, 'index'])->name('dashboard');
    Route::post('/api/finanzas/add', [FinanzasController::class, 'add']);
    Route::get('/api/finanzas/list', [FinanzasController::class, 'list']);
    Route::get('/api/finanzas/summary', [FinanzasController::class, 'summary']);
    Route::get('/api/finanzas/monthly', [FinanzasController::class, 'monthly']);
});



// Route::get('/', function () {
//     return view('welcome');
// });
