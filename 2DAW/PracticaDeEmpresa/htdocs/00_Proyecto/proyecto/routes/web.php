<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/login', function () {
    return redirect('/dashboard/login');
})->name('login');

// Rutas de autenticación con Google
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');

// Deshabilitar el registro público si se están usando rutas de autenticación estándar
Route::any('/register', function () {
    return redirect('/login');
});
