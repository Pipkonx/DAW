<?php

namespace App\Filament\Widgets;

use App\Models\Practice;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use App\Filament\Resources\PracticeResource;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;

class CalendarWidget extends FullCalendarWidget
{
    /**
     * Se elimina la propiedad $model para que el plugin no genere automáticamente
     * ninguna acción de creación (New Practice) ni permita interactuar con los eventos.
     */
    public Model | string | null $model = null;

    public Model | string | int | null $record = null;

    public function fetchEvents(array $fetchInfo): array
    {
        $usuarioActual = auth()->user();
        $query = Practice::query();

        // Aplicar la misma lógica de visibilidad que en PracticeResource
        if (!$usuarioActual->isAdmin()) {
            $query->where(function (Builder $subconsulta) use ($usuarioActual) {
                $subconsulta->where('user_id', $usuarioActual->id);

                if ($usuarioActual->isAlumno()) {
                    $alumnoId = $usuarioActual->alumno?->id;
                    $cursoId = $usuarioActual->alumno?->curso_id;
                    $subconsulta->orWhere('alumno_id', $alumnoId)
                      ->orWhere('curso_id', $cursoId)
                      ->orWhere('target_role', 'alumno');
                }

                if ($usuarioActual->isTutorPracticas()) {
                    $subconsulta->orWhereHas('alumno', function ($q) use ($usuarioActual) {
                        $q->whereHas('tutorPracticas', function ($tutorQ) use ($usuarioActual) {
                            $tutorQ->where('user_id', $usuarioActual->id);
                        });
                    })
                    ->orWhere('target_role', 'tutor_practicas');
                }

                if ($usuarioActual->isTutorCurso()) {
                    $subconsulta->orWhere('target_role', 'tutor_curso');
                }
            });
        }

        return $query
            ->where('starts_at', '>=', $fetchInfo['start'])
            ->where(function ($q) use ($fetchInfo) {
                $q->where('ends_at', '<=', $fetchInfo['end'])
                  ->orWhereNull('ends_at');
            })
            ->get()
            ->map(
                fn (Practice $practice) => [
                    'id' => $practice->id,
                    'title' => $practice->title,
                    'start' => $practice->starts_at,
                    'end' => $practice->ends_at ?? $practice->starts_at->addHour(),
                    'url' => PracticeResource::getUrl('view', ['record' => $practice]),
                    'shouldOpenUrlInNewTab' => false,
                ]
            )
            ->all();
    }

    public static function canView(): bool
    {
        return true;
    }

    protected function getHeaderActions(): array
    {
        return [
            // Se ha eliminado la opción de crear prácticas desde el Dashboard por petición del usuario
        ];
    }

    protected function getModalActions(): array
    {
        return [
            // Se eliminan todas las acciones de modal para que el calendario sea solo lectura
        ];
    }
}
