<?php

use App\Http\Controllers\AltaCtrl;
use App\Http\Controllers\InicioCtrl;
use Illuminate\Support\Facades\Route;


Route::any('/alta', [AltaCtrl::class, 'alta']);

Route::any('/', [InicioCtrl::class, 'index']);
