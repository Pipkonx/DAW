<?php

namespace App\Filament\Widgets;

use App\Models\ObservacionDiaria;
use App\Models\Curso;
use Filament\Widgets\Widget;

/**
 * @class CalendarioActividades
 * @brief Widget que muestra un calendario de actividades y fechas importantes.
 */
class CalendarioActividades extends Widget
{
    protected static string $view = 'filament.widgets.calendario-actividades';

    protected int | string | array $columnSpan = 'full';

    /**
     * @brief Obtiene los eventos para el calendario basados en el rol del usuario.
     * 
     * @return array Lista de eventos con título, fecha y tipo.
     */
    protected function obtenerEventos(): array
    {
        $usuario = auth()->user();
        $rol = $usuario->getRoleNames()->first();
        $eventos = [];

        // 1. Fechas de Cursos (Para todos)
        $cursos = Curso::all();
        foreach ($cursos as $curso) {
            $eventos[] = [
                'titulo' => 'Inicio: ' . $curso->nombre,
                'fecha' => $curso->fecha_inicio->format('Y-m-d'),
                'tipo' => 'curso_inicio',
                'color' => 'success',
            ];
            $eventos[] = [
                'titulo' => 'Fin: ' . $curso->nombre,
                'fecha' => $curso->fecha_fin->format('Y-m-d'),
                'tipo' => 'curso_fin',
                'color' => 'danger',
            ];
        }

        // 2. Actividades Diarias (Específicas del Alumno o Tutor)
        $queryObservaciones = ObservacionDiaria::query();

        if ($rol === 'alumno') {
            $queryObservaciones->where('alumno_id', $usuario->alumno?->id);
        } elseif ($rol === 'tutor_practicas') {
            $queryObservaciones->whereHas('alumno', fn($q) => $q->where('empresa_id', $usuario->perfilTutorPracticas?->empresa_id));
        }

        $observaciones = $queryObservaciones->get();
        foreach ($observaciones as $obs) {
            $eventos[] = [
                'titulo' => ($rol === 'alumno' ? 'Actividad: ' : 'Alumno ' . ($obs->alumno?->user?->name ?? 'desconocido') . ': ') . $obs->actividad,
                'fecha' => $obs->fecha->format('Y-m-d'),
                'tipo' => 'actividad',
                'color' => 'info',
            ];
        }

        return $eventos;
    }

    /**
     * @brief Pasa los eventos a la vista.
     */
    protected function getViewData(): array
    {
        return [
            'eventos' => $this->obtenerEventos(),
        ];
    }
}
