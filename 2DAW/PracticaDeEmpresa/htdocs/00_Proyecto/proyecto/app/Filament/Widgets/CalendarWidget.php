<?php

namespace App\Filament\Widgets;

use App\Models\Practice;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Illuminate\Database\Eloquent\Model;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use App\Filament\Resources\PracticeResource;
use Filament\Forms\Form;

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
        return Practice::query()
            ->where('starts_at', '>=', $fetchInfo['start'])
            ->where('ends_at', '<=', $fetchInfo['end'])
            ->get()
            ->map(
                fn (Practice $practice) => [
                    'id' => $practice->id,
                    'title' => $practice->title,
                    'start' => $practice->starts_at,
                    'end' => $practice->ends_at,
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
