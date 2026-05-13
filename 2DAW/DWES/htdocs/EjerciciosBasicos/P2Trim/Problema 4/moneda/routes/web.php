<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RedSocialController;
use App\Http\Controllers\Auth\InicioSesionController;
use App\Http\Controllers\Auth\RegistroController;
use App\Http\Controllers\CuotaController;

Route::get('/', function () {
    return redirect()->route('cuotas.index');
});

// Autenticación Estándar
Route::get('/login', [InicioSesionController::class, 'showLoginForm'])->name('login');
Route::post('/login', [InicioSesionController::class, 'login']);
Route::post('/logout', [InicioSesionController::class, 'logout'])->name('logout');

Route::get('/register', [RegistroController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegistroController::class, 'register']);

// Requisito 4.1: Gestión de Cuotas y Monedas
Route::middleware('auth')->group(function () {
    Route::get('/cuotas', [CuotaController::class, 'index'])->name('cuotas.index');
    Route::post('/cuotas', [CuotaController::class, 'store'])->name('cuotas.store');
    Route::post('/cuotas/{cuota}/pagar', [CuotaController::class, 'markAsPaid'])->name('cuotas.pagar');
    
    // Requisito 4.4: Pasarela de Pago
    Route::get('/pago/{cuota}/paypal', [App\Http\Controllers\PagoController::class, 'pagarConPaypal'])->name('pago.paypal');
    Route::get('/pago/exito', [App\Http\Controllers\PagoController::class, 'exito'])->name('pago.exito');
});

// SPA Javascript Consumer (Acceso libre para pruebas)
Route::get('/prueba-api', function (App\Services\ServicioDivisas $divisas) {
    $monedas = $divisas->getCurrencies();
    return view('prueba_api', compact('monedas'));
})->name('api.prueba');

// Requisito 4.3: Social Login
Route::get('/auth/{provider}/redirect', [RedSocialController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [RedSocialController::class, 'callback'])->name('social.callback');
