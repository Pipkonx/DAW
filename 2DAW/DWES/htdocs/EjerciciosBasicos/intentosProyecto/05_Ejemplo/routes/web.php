<?php


use App\Http\Controllers\C_Auth;
use App\Http\Controllers\C_Usuarios;
use App\Http\Controllers\C_Administrador;
use App\Http\Controllers\C_Operario;
use Illuminate\Support\Facades\Route;

Route::any('/', [C_Auth::class, 'login']);

Route::get('/admin/tareas', [C_Administrador::class, 'listar']);
Route::get('/admin/tareas/crear', [C_Administrador::class, 'crear']);
Route::post('/admin/tareas/crear', [C_Administrador::class, 'guardar'])->name('admin.tareas.guardar');
Route::get('/admin/tareas/editar', [C_Administrador::class, 'editar']);
Route::post('/admin/tareas/editar', [C_Administrador::class, 'actualizar'])->name('admin.tareas.actualizar');
Route::get('/admin/tareas/detalle', [C_Administrador::class, 'mostrar']);
Route::get('/admin/tareas/confirmarEliminar', [C_Administrador::class, 'confirmarEliminacion']);
Route::post('/admin/tareas/eliminar', [C_Administrador::class, 'eliminar']);
Route::post('/admin/tareas/eliminar-fichero', [C_Administrador::class, 'eliminarFichero'])->name('admin.tareas.eliminarFichero');
Route::any('/operario/tareas', [C_Operario::class, 'listar']);
Route::get('/operario/tareas/detalle', [C_Operario::class, 'mostrar']);
Route::get('/operario/tareas/editar', [C_Operario::class, 'editar']);
Route::post('/operario/tareas/editar', [C_Operario::class, 'actualizar'])->name('operario.tareas.actualizar');
Route::post('/operario/tareas/eliminar-fichero', [C_Operario::class, 'eliminarFichero'])->name('operario.tareas.eliminarFichero');

// Autenticación simple con contraseña plana, sesión y cookie para recordar la clave.
Route::any('/login', [C_Auth::class, 'login']);
Route::any('/logout', [C_Auth::class, 'logout']);