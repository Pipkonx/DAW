<?php

namespace App\Filament\Resources\IncidenciaResource\Pages;

use App\Filament\Resources\IncidenciaResource;
use Filament\Resources\Pages\CreateRecord;

use Filament\Notifications\Notification;

/**
 * @class CreateIncidencia
 * @brief Página para la creación de registros de Incidencias.
 */
class CreateIncidencia extends CreateRecord
{
    protected static string $resource = IncidenciaResource::class;

    protected function afterCreate(): void
    {
        Notification::make()
            ->success()
            ->title('Incidencia registrada')
            ->body("Se ha registrado una nueva incidencia para el alumno {$this->record->alumno->user->name}.")
            ->sendToDatabase(\Filament\Facades\Filament::auth()->user())
            ->send();
    }

    /**
     * @brief Obtiene la URL de redirección tras crear un registro.
     * 
     * @return string URL de la página de listado (Index).
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
