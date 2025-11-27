<?php


use App\Http\Controllers\ControladorInicio;
use App\Http\Controllers\ControladorAuth;
use App\Http\Controllers\ControladorUsuarios;
use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\OperarioController;
use Illuminate\Support\Facades\Route;

Route::any('/', [ControladorAuth::class, 'login']);

Route::get('/admin/tareas', [AdministradorController::class, 'listar']);
Route::get('/admin/tareas/crear', [AdministradorController::class, 'crear']);
Route::post('/admin/tareas/crear', [AdministradorController::class, 'guardar']);
Route::get('/admin/tareas/editar', [AdministradorController::class, 'editar']);
Route::post('/admin/tareas/editar', [AdministradorController::class, 'actualizar']);
Route::get('/admin/tareas/detalle', [AdministradorController::class, 'mostrar']);
Route::get('/admin/tareas/confirmarEliminar', [AdministradorController::class, 'confirmarEliminacion']);
Route::post('/admin/tareas/eliminar', [AdministradorController::class, 'eliminar']);
Route::any('/operario/tareas', [OperarioController::class, 'listar']);
Route::any('/operario/tareas/actualizar', [OperarioController::class, 'actualizar']);

// Controlador Tareas Eliminado
// He dividio los controladores para que no sean tan complejos
// Route::any('/tareas', [ControladorTareas::class, 'listar']);
// Route::any('/tareas/crear', [ControladorTareas::class, 'crear']);
// Route::any('/tareas/editar', [ControladorTareas::class, 'editar']);
// Route::get('/tareas/detalle', [ControladorTareas::class, 'detalle']);
// Route::get('/tareas/confirmarEliminar', [ControladorTareas::class, 'confirmarEliminar']);
// Route::post('/tareas/eliminar', [ControladorTareas::class, 'eliminar']);
// Route::any('/alta', [ControladorTareas::class, 'crear']);

// Autenticación simple
Route::any('/login', [ControladorAuth::class, 'login']);
Route::any('/logout', [ControladorAuth::class, 'logout']);

// Gestión de usuarios (Admin)
Route::any('/usuarios', [ControladorUsuarios::class, 'listar']);
Route::any('/usuarios/crear', [ControladorUsuarios::class, 'crear']);

Route::any('/usuarios/editar', [ControladorUsuarios::class, 'editar']);
Route::post('/usuarios/eliminar', [ControladorUsuarios::class, 'eliminar']);
