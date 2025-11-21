<?php

use App\Http\Controllers\ControladorTareas;
use App\Http\Controllers\ControladorInicio;
use App\Http\Controllers\ControladorAuth;
use Illuminate\Support\Facades\Route;

Route::any('/', [ControladorAuth::class, 'login']);

Route::any('/tareas', [ControladorTareas::class, 'listar']);
Route::any('/tareas/crear', [ControladorTareas::class, 'crear']);
Route::any('/tareas/{id}/editar', [ControladorTareas::class, 'editar']);
Route::get('/tareas/{id}/eliminar', [ControladorTareas::class, 'confirmarEliminar']);
Route::post('/tareas/{id}/eliminar', [ControladorTareas::class, 'eliminar']);

Route::any('/alta', [ControladorTareas::class, 'crear']);

// Autenticación simple
Route::any('/login', [ControladorAuth::class, 'login']);
Route::any('/logout', [ControladorAuth::class, 'logout']);
