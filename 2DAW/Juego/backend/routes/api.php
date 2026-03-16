<?php

use App\Http\Controllers\ProgressController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/progress', [ProgressController::class, 'index']);
    Route::post('/progress/save', [ProgressController::class, 'store']);
});

require __DIR__.'/auth.php';
