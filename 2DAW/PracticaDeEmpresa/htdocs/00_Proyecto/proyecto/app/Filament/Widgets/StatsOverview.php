<?php

namespace App\Filament\Widgets;

use App\Models\Alumno;
use App\Models\Incidencia;
use App\Models\Evaluacion;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $incidenciasAbiertas = Incidencia::where('estado', 'abierta')->count();
        $notaMediaGlobal = Evaluacion::avg('nota_final') ?? 0;

        return [
            Stat::make('Total Alumnos', Alumno::count())
                ->description('Alumnos registrados en el sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
            Stat::make('Incidencias Abiertas', $incidenciasAbiertas)
                ->description($incidenciasAbiertas > 0 ? 'Requieren atención' : 'Sin pendientes')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($incidenciasAbiertas > 0 ? 'danger' : 'success'),
            Stat::make('Nota Media Global', number_format($notaMediaGlobal, 2))
                ->description('Promedio de todas las evaluaciones')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color($notaMediaGlobal >= 5 ? 'success' : 'warning'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isTutorCurso();
    }
}
