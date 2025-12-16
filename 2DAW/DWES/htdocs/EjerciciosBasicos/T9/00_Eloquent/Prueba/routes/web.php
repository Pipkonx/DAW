<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlumnoController;

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


