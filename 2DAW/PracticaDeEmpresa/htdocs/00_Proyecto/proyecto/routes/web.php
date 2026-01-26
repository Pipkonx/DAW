<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

// Deshabilitar el registro público si se están usando rutas de autenticación estándar
Route::any('/register', function () {
    return redirect('/admin/login');
});
