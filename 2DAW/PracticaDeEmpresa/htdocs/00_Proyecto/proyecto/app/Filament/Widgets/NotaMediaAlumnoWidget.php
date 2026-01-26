<?php

namespace App\Filament\Widgets;

use App\Models\Alumno;
use App\Models\Evaluacion;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NotaMediaAlumnoWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        
        // Obtenemos el registro de Alumno asociado al usuario autenticado
        $alumno = Alumno::where('user_id', $user->id)->first();
        
        if (!$alumno) {
            return [];
        }

        $notaMedia = Evaluacion::where('alumno_id', $alumno->id)
            ->avg('nota_final');

        return [
            Stat::make('Mi Nota Media', number_format($notaMedia ?? 0, 2))
                ->description('Promedio de todas tus evaluaciones')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color($notaMedia >= 5 ? 'success' : 'danger'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->isAlumno();
    }
}
