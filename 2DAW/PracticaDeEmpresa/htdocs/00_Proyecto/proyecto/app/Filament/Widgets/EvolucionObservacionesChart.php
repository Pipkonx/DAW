<?php

namespace App\Filament\Widgets;

use App\Models\ObservacionDiaria;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class EvolucionObservacionesChart extends ChartWidget
{
    protected static ?string $heading = 'Actividad de Observaciones Diarias';

    protected function getData(): array
    {
        // Obtenemos los últimos 7 días
        $days = collect(range(6, 0))->map(fn ($i) => now()->subDays($i)->format('Y-m-d'));

        $data = ObservacionDiaria::select(DB::raw('DATE(fecha) as date'), DB::raw('count(*) as count'))
            ->where('fecha', '>=', now()->subDays(6))
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date');

        // Rellenamos los días que no tienen datos con 0
        $chartData = $days->map(fn ($day) => $data->get($day, 0));

        return [
            'datasets' => [
                [
                    'label' => 'Observaciones Creadas',
                    'data' => $chartData->toArray(),
                    'borderColor' => '#4BC0C0',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $days->map(fn ($day) => Carbon::parse($day)->format('d/m'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public static function canView(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isTutorCurso();
    }
}
