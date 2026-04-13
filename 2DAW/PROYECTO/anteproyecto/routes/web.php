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
use App\Http\Controllers\MarketController;
use App\Http\Controllers\MarketDataController;
use App\Http\Controllers\AiAnalystController;
use App\Http\Controllers\TransactionActionController;
use App\Http\Controllers\TransactionExportController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminAnalyticsController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\MarketAssetController;
use App\Http\Controllers\FamousPortfolioController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\Profile\SecurityController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
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
})->name('welcome');

// Panel de Control (Dashboard) - Usando Controller
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/api/dashboard/transactions', [DashboardController::class, 'getTransactions'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard.transactions');

// Transacciones
Route::get('/transactions/export', [TransactionExportController::class, 'export'])
    ->middleware(['auth', 'verified'])
    ->name('transactions.export');

Route::post('/transactions/import', [\App\Http\Controllers\TransactionActionController::class, 'import'])
    ->middleware(['auth', 'verified'])
    ->name('transactions.import');

Route::get('/transactions', [TransactionController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('transactions.index');

Route::get('/transactions/performance', [TransactionController::class, 'performance'])
    ->middleware(['auth', 'verified'])
    ->name('transactions.performance');

Route::get('/transactions/allocation', [TransactionController::class, 'allocation'])
    ->middleware(['auth', 'verified'])
    ->name('transactions.allocation');

Route::post('/transactions', [TransactionActionController::class, 'store'])
    ->middleware(['auth'])
    ->name('transactions.store');

Route::put('/transactions/{transaction}', [TransactionActionController::class, 'update'])
    ->middleware(['auth'])
    ->name('transactions.update');

Route::delete('/transactions/bulk-destroy', [TransactionActionController::class, 'bulkDestroy'])
    ->middleware(['auth'])
    ->name('transactions.bulk-destroy');

Route::delete('/transactions/{transaction}', [TransactionActionController::class, 'destroy'])
    ->middleware(['auth'])
    ->name('transactions.destroy');

// Análisis de Gastos
Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
Route::get('/api/expenses/transactions', [ExpenseController::class, 'getTransactions'])
    ->middleware(['auth', 'verified'])
    ->name('expenses.transactions');

// Analista IA (Nuevo)
Route::get('/ai-analyst', [AiAnalystController::class, 'index'])->name('ai-analyst.index');
Route::get('/api/ai-analyst/report', [AiAnalystController::class, 'generateReport'])->name('ai-analyst.report');

// Rutas de Perfil de Usuario (Autenticadas)
Route::middleware(['auth', 'verified'])->group(function () {
    // Mercados
    Route::get('/markets', [MarketController::class, 'index'])->name('markets.index');

    // Market Data API (Search & Price)
    Route::get('/api/market/search', [MarketDataController::class, 'search'])->name('market.search');
    Route::get('/api/market/price', [MarketDataController::class, 'getPrice'])->name('market.price');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/subscription/renew', [ProfileController::class, 'renewSubscription'])->name('profile.subscription.renew');
    Route::post('/profile/subscription/cancel', [ProfileController::class, 'cancelSubscription'])->name('profile.subscription.cancel');

    // Rutas de Carteras
    Route::post('/portfolios/preview-import', [PortfolioController::class, 'previewImport'])->name('portfolios.preview-import');
    Route::post('/portfolios', [PortfolioController::class, 'store'])->name('portfolios.store');
    Route::put('/portfolios/{portfolio}', [PortfolioController::class, 'update'])->name('portfolios.update');
    Route::delete('/portfolios/{portfolio}', [PortfolioController::class, 'destroy'])->name('portfolios.destroy');

    // Rutas de Activos y Social
    Route::get('/assets/{ticker}', [MarketAssetController::class, 'show'])->name('assets.show');
    Route::get('/api/assets/{ticker}/chart', [MarketAssetController::class, 'getChartRange'])->name('assets.chart-data');

    Route::get('/famous-portfolios/{slug}', [FamousPortfolioController::class, 'show'])->name('famous-portfolios.show');
    Route::post('/famous-portfolios/{slug}/follow', [FamousPortfolioController::class, 'toggleFollow'])->name('famous-portfolios.follow');

    Route::get('/social/feed', [SocialController::class, 'index'])->name('social.feed');
    Route::post('/social/post', [SocialController::class, 'storePost'])->name('social.post');
    Route::put('/social/post/{post}', [SocialController::class, 'updatePost'])->name('social.update');
    Route::delete('/social/post/{post}', [SocialController::class, 'deletePost'])->name('social.delete');
    Route::post('/social/comment/{post}', [SocialController::class, 'storeComment'])->name('social.comment');
    Route::post('/social/like', [SocialController::class, 'toggleLike'])->name('social.like');
    Route::post('/social/repost/{post}', [SocialController::class, 'toggleRepost'])->name('social.repost');
    Route::post('/social/bookmark/{post}', [SocialController::class, 'toggleBookmark'])->name('social.bookmark');
    Route::post('/social/pin/{post}', [SocialController::class, 'togglePin'])->name('social.pin');
    Route::post('/social/report', [SocialController::class, 'reportContent'])->name('social.report');

    // Muro de Usuario y Perfil Social
    Route::get('/perfil/{username?}', [ProfileController::class, 'show'])
        ->name('social.profile')
        ->where('username', '.*');
    Route::patch('/profile/social', [ProfileController::class, 'updateSocial'])->name('profile.social.update');
    Route::post('/profile/{user}/follow', [ProfileController::class, 'toggleFollow'])->name('profile.social.follow');
    Route::post('/profile/{user}/block', [ProfileController::class, 'block'])->name('profile.social.block');

    Route::delete('/assets/bulk-destroy', [App\Http\Controllers\AssetController::class, 'bulkDestroy'])->name('assets.bulk-destroy');
    Route::put('/assets/{asset}', [App\Http\Controllers\AssetController::class, 'update'])->name('assets.update');
    Route::delete('/assets/{asset}', [App\Http\Controllers\AssetController::class, 'destroy'])->name('assets.destroy');

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

    // Rutas de Suscripción (Pagos)
    Route::get('/plans', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');

    // Rutas de 2FA Login (Cerradas pero con acceso temporal por sesión)
    Route::get('/login/2fa', [TwoFactorChallengeController::class, 'create'])->name('login.2fa');
    Route::post('/login/2fa', [TwoFactorChallengeController::class, 'store'])->name('login.2fa.store');

    // Rutas de Seguridad (Real 2FA TOTP)
    Route::get('/profile/security', [SecurityController::class, 'index'])->name('profile.security');
    Route::post('/profile/security/2fa/setup', [SecurityController::class, 'setup2fa'])->name('profile.security.setup2fa');
    Route::post('/profile/security/2fa/activate', [SecurityController::class, 'activate2fa'])->name('profile.security.activate2fa');
    Route::post('/profile/security/2fa/disable', [SecurityController::class, 'disable2fa'])->name('profile.security.disable2fa');

    // Rutas de Soporte (Nuevo)
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::get('/support/tickets/{ticket}', [SupportController::class, 'show'])->name('support.show');
    Route::post('/support/tickets', [SupportController::class, 'store'])->name('support.store');
    Route::post('/support/tickets/{ticket}/reply', [SupportController::class, 'reply'])->name('support.reply');

    // Onboarding
    Route::post('/onboarding/complete', function () {
        auth()->user()->update(['onboarding_completed_at' => now()]);
        return back();
    })->name('onboarding.complete');
});

// Panel de Administración (Protegido por Middleware Admin)
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Gestión de Usuarios
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::post('/users/{user}/update-subscription', [AdminController::class, 'updateSubscription'])->name('users.update-subscription');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');

    // Gestión administrativa de Tickets
    Route::get('/tickets', [\App\Http\Controllers\Admin\TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [\App\Http\Controllers\Admin\TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/reply', [\App\Http\Controllers\Admin\TicketController::class, 'reply'])->name('tickets.reply');
    Route::post('/tickets/{ticket}/close', [\App\Http\Controllers\Admin\TicketController::class, 'close'])->name('tickets.close');

    // Backups
    Route::post('/backup', [AdminController::class, 'generateBackup'])->name('backup.generate');
    Route::get('/backup/download/{filename}', [AdminController::class, 'downloadBackup'])->name('backup.download');
    Route::delete('/backup/{filename}', [AdminController::class, 'deleteBackup'])->name('backup.delete');
    Route::post('/backup/restore/{filename}', [AdminController::class, 'restoreBackup'])->name('backup.restore');
    Route::post('/backup/import', [AdminController::class, 'importBackup'])->name('backup.import');
    Route::post('/backup/restore-direct', [AdminController::class, 'restoreDirect'])->name('backup.restore.direct');

    // Mantenimiento de Sistema
    Route::post('/system/optimize', [AdminController::class, 'optimizeDb'])->name('system.optimize');
    Route::post('/system/clear-cache', [AdminController::class, 'clearCache'])->name('system.clear-cache');
    Route::get('/system/logs', [AdminController::class, 'getSystemLogs'])->name('system.logs');

    // Gestión de Reportes
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::delete('/reports/{report}/dismiss', [AdminReportController::class, 'dismiss'])->name('reports.dismiss');
    Route::delete('/reports/{report}/action', [AdminReportController::class, 'destroyContent'])->name('reports.action');

    // Analíticas
    Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('analytics');
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

require __DIR__ . '/auth.php';
