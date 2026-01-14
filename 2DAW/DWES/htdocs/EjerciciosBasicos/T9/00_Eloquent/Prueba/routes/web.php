<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ProvControl;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/alumnos', [AlumnoController::class, 'index']);
Route::get('/alumnos/pluck', [AlumnoController::class, 'pluck']);
Route::get('/alumnos/select', [AlumnoController::class, 'select']);
Route::get('/alumnos/find', [AlumnoController::class, 'find']);
Route::get('/alumnos/findMany', [AlumnoController::class, 'findMany']);
Route::get('/alumnos/findNombre', [AlumnoController::class, 'findNombre']);
Route::get('/alumnos/findNacimiento', [AlumnoController::class, 'findNacimiento']);
Route::get('/alumnos/borrar', [AlumnoController::class, 'borrar']);
Route::get('/alumnos/borrar7', [AlumnoController::class, 'borrar7']);
Route::get('/alumnos/crear', [AlumnoController::class, 'crear']);


Route::resource("photo", ProvControl::class);



// REVISAR DOC
// https://laravel.com/docs/12.x/controllers\

// php artisan make:controller ProvControl --resource

// https://laravel.com/docs/12.x/eloquent-resources#main-content

// revisar el tema de docker con sail en laravel con Sail
// https://laravel.com/docs/12.x/sail

// https://laravel.com/docs/12.x/authentication
// aconsejable crear un proyecto nuevo de laravel con vue y laravel de auth y con npm

// revisar el middleware

//El examen eloquent relaciones formulario  validando etc haciendo to el proceso

// tenemos que agregar el laravel ui

// leer sobre 
//https://vite.dev/

// leer m'as sobre 
// https://filamentphp.com/docs
// y para el crud
// https://nova.laravel.com/