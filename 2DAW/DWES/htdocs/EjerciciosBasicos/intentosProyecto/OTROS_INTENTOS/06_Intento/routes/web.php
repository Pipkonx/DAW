<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('tareas.index');
});

Route::get('/debug', function () {
    return 'OK';
});

// Rutas adaptadas desde 01_MVC_PDO_Singleton
Route::get('/usuarios', [\App\Http\Controllers\UsuarioController::class, 'index'])->name('usuarios.index');
Route::get('/tareas', [\App\Http\Controllers\TareaController::class, 'index'])->name('tareas.index');
