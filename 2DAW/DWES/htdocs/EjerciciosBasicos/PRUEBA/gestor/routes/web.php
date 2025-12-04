<?php


use App\Http\Controllers\C_Auth;
use App\Http\Controllers\C_Usuarios;
use App\Http\Controllers\C_Administrador;
use App\Http\Controllers\C_Operario;
use Illuminate\Support\Facades\Route;


Route::any('/', [C_Auth::class, 'login']);

Route::get('/admin/tareas', [C_Administrador::class, 'listar']);
Route::get('/admin/tareas/crear', [C_Administrador::class, 'crear']);
Route::post('/admin/tareas/crear', [C_Administrador::class, 'guardar']);
Route::get('/admin/tareas/editar', [C_Administrador::class, 'editar']);
Route::post('/admin/tareas/editar', [C_Administrador::class, 'actualizar']);
Route::get('/admin/tareas/detalle', [C_Administrador::class, 'mostrar']);
Route::get('/admin/tareas/confirmarEliminar', [C_Administrador::class, 'confirmarEliminacion']);
Route::post('/admin/tareas/eliminar', [C_Administrador::class, 'eliminar']);
Route::post('/admin/tareas/eliminar-fichero', [C_Administrador::class, 'eliminarFichero']);

// Rutas para la gestión de usuarios
Route::get('/admin/usuarios', [C_Usuarios::class, 'listar']);
Route::get('/admin/usuarios/crear', [C_Usuarios::class, 'crear']);
Route::post('/admin/usuarios/crear', [C_Usuarios::class, 'guardar']);
Route::get('/admin/usuarios/editar', [C_Usuarios::class, 'editar']);
Route::post('/admin/usuarios/editar', [C_Usuarios::class, 'actualizar']);
Route::get('/admin/usuarios/confirmarEliminar', [C_Usuarios::class, 'confirmarEliminacion']);
Route::post('/admin/usuarios/eliminar', [C_Usuarios::class, 'eliminar']);

Route::any('/operario/tareas', [C_Operario::class, 'listar']);

Route::get('/operario/tareas/detalle', [C_Operario::class, 'mostrar']);
Route::get('/operario/tareas/editar', [C_Operario::class, 'editar']);
Route::post('/operario/tareas/editar', [C_Operario::class, 'actualizar']);
Route::post('/operario/tareas/eliminar-fichero', [C_Operario::class, 'eliminarFichero']);


// Autenticación simple con contraseña plana, sesión y cookie para recordar la clave.
Route::any('/login', [C_Auth::class, 'login']);
Route::any('/logout', [C_Auth::class, 'logout']);