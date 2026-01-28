<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\BackupPolicy;
use App\Models\Incidencia;
use App\Models\Practice;
use App\Observers\IncidenciaObserver;
use App\Observers\PracticeObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(\ShuvroRoy\FilamentSpatieLaravelBackup\Models\Backup::class, BackupPolicy::class);
        Incidencia::observe(IncidenciaObserver::class);
        Practice::observe(PracticeObserver::class);
    }
}
