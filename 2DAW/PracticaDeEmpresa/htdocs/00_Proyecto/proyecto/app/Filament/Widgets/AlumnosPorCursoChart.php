<?php

namespace App\Filament\Widgets;

use App\Models\Curso;
use Filament\Widgets\ChartWidget;

class AlumnosPorCursoChart extends ChartWidget
{
    protected static ?string $heading = 'Distribución de Alumnos por Curso';

    protected function getData(): array
    {
        $cursos = Curso::withCount('alumnos')->get();

        return [
            'datasets' => [
                [
                    'label' => 'Alumnos',
                    'data' => $cursos->pluck('alumnos_count')->toArray(),
                    'backgroundColor' => [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                    ],
                ],
            ],
            'labels' => $cursos->pluck('nombre')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    public static function canView(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isTutorCurso();
    }
}
