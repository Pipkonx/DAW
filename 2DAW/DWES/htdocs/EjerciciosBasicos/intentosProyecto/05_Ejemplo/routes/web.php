<?php

use App\Http\Controllers\ControladorTareas;
use App\Http\Controllers\ControladorInicio;
use App\Http\Controllers\ControladorAuth;
use App\Http\Controllers\ControladorUsuarios;
use Illuminate\Support\Facades\Route;

Route::any('/', [ControladorAuth::class, 'login']);

Route::any('/tareas', [ControladorTareas::class, 'listar']);
Route::any('/tareas/crear', [ControladorTareas::class, 'crear']);
Route::any('/tareas/editar', [ControladorTareas::class, 'editar']);
Route::get('/tareas/detalle', [ControladorTareas::class, 'detalle']);
Route::get('/tareas/confirmarEliminar', [ControladorTareas::class, 'confirmarEliminar']);
Route::post('/tareas/eliminar', [ControladorTareas::class, 'eliminar']);

Route::any('/alta', [ControladorTareas::class, 'crear']);

// Autenticación simple
Route::any('/login', [ControladorAuth::class, 'login']);
Route::any('/logout', [ControladorAuth::class, 'logout']);

// Gestión de usuarios (Admin)
Route::any('/usuarios', [ControladorUsuarios::class, 'listar']);
Route::any('/usuarios/crear', [ControladorUsuarios::class, 'crear']);

Route::any('/usuarios/editar', [ControladorUsuarios::class, 'editar']);
Route::post('/usuarios/eliminar', [ControladorUsuarios::class, 'eliminar']);
