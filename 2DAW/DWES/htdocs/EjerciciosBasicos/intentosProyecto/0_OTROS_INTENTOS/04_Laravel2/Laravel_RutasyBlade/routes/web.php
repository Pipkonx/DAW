<?php

use App\Http\Controllers\Ctrl1;
use App\Http\Controllers\Ctrl2;
use Illuminate\Support\Facades\Route;

Route::any('/', [Ctrl1::class, 'action1']);

Route::any('/ej01', [Ctrl2::class, 'ej01']);
Route::any('/ej02', [Ctrl2::class, 'ej02']);
Route::any('/ej02_1', [Ctrl2::class, 'ej02_1']);
Route::any('/ej03', [Ctrl2::class, 'ej03']);
Route::any('/ej04', [Ctrl2::class, 'ej04']);
Route::any('/ej10', [Ctrl2::class, 'ej10']);
Route::any('/ej11', [Ctrl2::class, 'ej11']);
