<?php

namespace App\Filament\Widgets;

use App\Models\Alumno;
use App\Models\Empresa;
use App\Models\Incidencia;
use App\Models\Evaluacion;
use App\Models\ObservacionDiaria;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * @class EstadisticasPersonalizadas
 * @brief Widget que muestra estadísticas adaptadas según el rol del usuario autenticado.
 */
class EstadisticasPersonalizadas extends BaseWidget
{
    /**
     * @brief Genera las estadísticas para el Dashboard basadas en el rol del usuario.
     * 
     * @return array Lista de objetos Stat.
     */
    protected function getStats(): array
    {
        $usuario = auth()->user();
        $rol = $usuario->getRoleNames()->first();

        return match ($rol) {
            'alumno' => $this->obtenerEstadisticasAlumno($usuario),
            'admin', 'tutor_curso' => $this->obtenerEstadisticasTutoresAdmin($usuario),
            'tutor_practicas' => $this->obtenerEstadisticasTutorEmpresa($usuario),
            default => [],
        };
    }

    /**
     * @brief Obtiene estadísticas específicas para el rol Alumno.
     * 
     * @param \App\Models\User $usuario Usuario autenticado.
     * @return array Estadísticas de nota media, horas e incidencias.
     */
    protected function obtenerEstadisticasAlumno($usuario): array
    {
        $alumno = $usuario->alumno;
        if (!$alumno) return [];

        $notaMedia = Evaluacion::where('alumno_id', $alumno->id)->avg('nota_final') ?? 0;
        $horasTotales = ObservacionDiaria::where('alumno_id', $alumno->id)->sum('horas') ?? 0;
        $incidenciasAbiertas = Incidencia::where('alumno_id', $alumno->id)->where('estado', 'abierta')->count();

        return [
            Stat::make('Nota Media', number_format($notaMedia, 2))
                ->description('Tu promedio actual')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color($notaMedia >= 5 ? 'success' : 'danger'),
            Stat::make('Horas Realizadas', $horasTotales . ' h')
                ->description('Total de horas en empresas')
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
            Stat::make('Incidencias Abiertas', $incidenciasAbiertas)
                ->description('Pendientes de resolución')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color($incidenciasAbiertas > 0 ? 'warning' : 'success'),
        ];
    }

    /**
     * @brief Obtiene estadísticas globales para Admin y Tutor de Curso.
     * 
     * @param \App\Models\User $usuario Usuario autenticado.
     * @return array Estadísticas de alumnos, empresas e incidencias.
     */
    protected function obtenerEstadisticasTutoresAdmin($usuario): array
    {
        $totalAlumnos = Alumno::count();
        $empresasActivas = Empresa::where('activa', true)->count();
        $incidenciasSinResolver = Incidencia::where('estado', '!=', 'resuelta')->count();

        return [
            Stat::make('Alumnos Asignados', $totalAlumnos)
                ->description('Total en el sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Empresas Activas', $empresasActivas)
                ->description('Convenios vigentes')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('success'),
            Stat::make('Alertas de Incidencias', $incidenciasSinResolver)
                ->description('Sin resolver en el sistema')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color($incidenciasSinResolver > 0 ? 'danger' : 'success'),
        ];
    }

    /**
     * @brief Obtiene estadísticas para el Tutor de Empresa.
     * 
     * @param \App\Models\User $usuario Usuario autenticado.
     * @return array Estadísticas de alumnos de su empresa.
     */
    protected function obtenerEstadisticasTutorEmpresa($usuario): array
    {
        $empresaId = $usuario->perfilTutorPracticas?->empresa_id;
        $alumnosEmpresa = Alumno::where('empresa_id', $empresaId)->count();
        $incidenciasEmpresa = Incidencia::whereHas('alumno', fn($q) => $q->where('empresa_id', $empresaId))
            ->where('estado', 'abierta')->count();

        return [
            Stat::make('Mis Alumnos', $alumnosEmpresa)
                ->description('Asignados a tu empresa')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Incidencias Empresa', $incidenciasEmpresa)
                ->description('Reportadas en tu centro')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color($incidenciasEmpresa > 0 ? 'danger' : 'success'),
        ];
    }
}
