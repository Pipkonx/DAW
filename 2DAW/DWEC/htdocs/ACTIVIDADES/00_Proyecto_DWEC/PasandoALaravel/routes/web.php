<?php

use App\Http\Controllers\FinanzasController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::any('/', [FinanzasController::class, 'index']);
Route::any('/', [UsuarioController::class, 'index']);



// Route::get('/', function () {
//     return view('welcome');
// });
