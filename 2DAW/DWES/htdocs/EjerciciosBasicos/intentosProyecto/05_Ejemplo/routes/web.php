<?php

use App\Http\Controllers\ControladorTareas;
use App\Http\Controllers\ControladorInicio;
use Illuminate\Support\Facades\Route;

Route::any('/', [ControladorInicio::class, 'inicio']);

Route::any('/tareas', [ControladorTareas::class, 'listar']);
Route::any('/tareas/crear', [ControladorTareas::class, 'crear']);
Route::any('/tareas/{id}/editar', [ControladorTareas::class, 'editar']);
Route::any('/tareas/{id}/eliminar', [ControladorTareas::class, 'eliminar']);

Route::any('/alta', [ControladorTareas::class, 'crear']);
