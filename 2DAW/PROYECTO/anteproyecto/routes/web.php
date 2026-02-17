<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\DashboardController; // Importar
use App\Http\Controllers\TransactionController; // Importar
use App\Http\Controllers\ExpenseController; // Importar
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\FinancialPlanningController;
use App\Http\Controllers\CategoryController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar las rutas web para tu aplicación.
| Estas rutas son cargadas por el RouteServiceProvider dentro de un grupo
| que contiene el grupo de middleware "web".
|
*/

// Página de inicio (Landing Page)
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Panel de Control (Dashboard) - Usando Controller
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Transacciones
Route::get('/transactions/export', [TransactionController::class, 'export'])
    ->middleware(['auth', 'verified'])
    ->name('transactions.export');

Route::get('/transactions', [TransactionController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('transactions.index');

Route::post('/transactions', [TransactionController::class, 'store'])
    ->middleware(['auth'])
    ->name('transactions.store');

Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])
    ->middleware(['auth'])
    ->name('transactions.update');

// Análisis de Gastos
Route::get('/expenses', [ExpenseController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('expenses.index');

// Rutas de Perfil de Usuario (Autenticadas)
use App\Http\Controllers\MarketDataController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Market Data API (Search & Price)
    Route::get('/api/market/search', [MarketDataController::class, 'search'])->name('market.search');
    Route::get('/api/market/price', [MarketDataController::class, 'getPrice'])->name('market.price');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de Carteras
    Route::post('/portfolios', [PortfolioController::class, 'store'])->name('portfolios.store');
    Route::put('/portfolios/{portfolio}', [PortfolioController::class, 'update'])->name('portfolios.update');
    Route::delete('/portfolios/{portfolio}', [PortfolioController::class, 'destroy'])->name('portfolios.destroy');

    // Rutas de Activos
    Route::put('/assets/{asset}', [App\Http\Controllers\AssetController::class, 'update'])->name('assets.update');

    // Rutas de Planificación Financiera y Cuentas Bancarias
    Route::get('/financial-planning', [FinancialPlanningController::class, 'index'])->name('financial-planning.index');
    Route::post('/financial-planning/settings', [FinancialPlanningController::class, 'updateSettings'])->name('financial-planning.update-settings');
    Route::resource('bank-accounts', BankAccountController::class)->only(['store', 'update', 'destroy']);

    // Rutas de Categorías
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::patch('/categories/{category}/toggle', [CategoryController::class, 'toggleActive'])->name('categories.toggle');
});

// Rutas de Autenticación con Google (Socialite)
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

// Rutas para Generación de PDF (Anteproyecto)
Route::get('/anteproyecto/pdf', [PdfController::class, 'download'])->name('anteproyecto.download');
Route::get('/anteproyecto/stream', [PdfController::class, 'stream'])->name('anteproyecto.stream');

// Rutas Legales (Páginas Estáticas)
Route::controller(LegalController::class)->group(function () {
    Route::get('/terms', 'terms')->name('legal.terms');
    Route::get('/privacy', 'privacy')->name('legal.privacy');
    Route::get('/legal-notice', 'notice')->name('legal.notice');
});

require __DIR__.'/auth.php';
