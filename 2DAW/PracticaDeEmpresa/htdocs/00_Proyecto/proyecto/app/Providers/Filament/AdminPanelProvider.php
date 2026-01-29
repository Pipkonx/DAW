<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use ShuvroRoy\FilamentSpatieLaravelBackup\FilamentSpatieLaravelBackupPlugin;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Illuminate\Support\Facades\Blade;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('dashboard')
            ->login()
            ->profile(\App\Filament\Pages\EditProfile::class)
            ->unsavedChangesAlerts()
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->colors([
                'primary' => Color::Indigo,
            ])
            ->userMenuItems([
                'chat' => \Filament\Navigation\MenuItem::make()
                    ->label('Chat Interno')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->url(fn() => \App\Filament\Pages\ChatPage::getUrl()),
            ])
            ->renderHook(
                'panels::user-menu.before',
                fn (): string => Blade::render('
                    @php
                        $unreadTotal = cache()->remember("unread_count_" . auth()->id(), 60, function() {
                            return \App\Models\Message::where(\'receiver_id\', auth()->id())
                                ->where(\'is_read\', false)
                                ->count();
                        });
                    @endphp
                    @if($unreadTotal > 0)
                        <a href="{{ \App\Filament\Pages\ChatPage::getUrl() }}" 
                           class="relative flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-indigo-600 transition-all hover:scale-110 mr-3 group"
                           title="Tienes {{ $unreadTotal }} mensajes sin leer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 group-hover:animate-pulse">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3h9m-9 3h3m-6.75 4.125a3 3 0 0 0 3 3h7.5a3 3 0 0 0 3-3V6.75a3 3 0 0 0-3-3h-7.5a3 3 0 0 0-3 3v11.625Z" />
                            </svg>
                            <span class="absolute -top-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-600 text-[11px] font-black text-white ring-2 ring-white dark:ring-gray-900 shadow-lg animate-bounce group-hover:bg-red-500">
                                {{ $unreadTotal }}
                            </span>
                        </a>
                    @endif
                '),
            )
            ->renderHook(
                'panels::auth.login.form.after',
                fn (): string => Blade::render('
                    <div class="mt-4">
                        <div class="relative flex items-center justify-center py-2">
                            <div class="flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                            <span class="flex-shrink mx-4 text-sm text-gray-400">O bien</span>
                            <div class="flex-grow border-t border-gray-300 dark:border-gray-700"></div>
                        </div>
                        <a href="{{ route(\'google.login\') }}" 
                           class="flex items-center justify-center w-full gap-3 px-4 py-2 mt-2 text-sm font-semibold transition-all duration-200 bg-white border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <svg class="w-5 h-5" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            <span>Iniciar sesión con Google</span>
                        </a>
                    </div>
                '),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\EstadisticasPersonalizadas::class,
                \App\Filament\Widgets\AccionesPrincipales::class,
                \App\Filament\Widgets\CalendarWidget::class,
                \App\Filament\Widgets\CalendarioActividades::class,
                \App\Filament\Widgets\AlumnosPorCursoChart::class,
                \App\Filament\Widgets\EvolucionObservacionesChart::class,
            ])
            ->plugins([
                FilamentSpatieLaravelBackupPlugin::make()
                    ->usingPage(\ShuvroRoy\FilamentSpatieLaravelBackup\Pages\Backups::class)
                    ->usingQueue('default')
                    ->authorize(fn () => auth()->user()->isAdmin()),
                FilamentFullCalendarPlugin::make()
                    ->schedulerLicenseKey('GPL-My-Project-Is-Open-Source')
                    ->selectable()
                    ->editable(),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
