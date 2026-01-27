<?php

namespace App\Filament\Resources\ObservacionDiariaResource\Pages;

use App\Filament\Resources\ObservacionDiariaResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * @class CreateObservacionDiaria
 * @brief Página para la creación de Observaciones Diarias.
 */
use Filament\Notifications\Notification;

class CreateObservacionDiaria extends CreateRecord
{
    protected static string $resource = ObservacionDiariaResource::class;

    protected function afterCreate(): void
    {
        Notification::make()
            ->success()
            ->title('Observación diaria registrada')
            ->body("Se ha registrado una nueva observación para el alumno {$this->record->alumno->user->name}.")
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
