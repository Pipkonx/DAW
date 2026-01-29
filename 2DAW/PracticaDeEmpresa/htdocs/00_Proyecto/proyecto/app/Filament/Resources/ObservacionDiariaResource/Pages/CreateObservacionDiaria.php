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

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Observación diaria registrada')
            ->body("Se ha registrado una nueva observación para el alumno " . ($this->record->alumno?->user?->name ?? 'desconocido') . ".")
            ->sendToDatabase(\Filament\Facades\Filament::auth()->user());
    }

    /**
     * @brief Modifica los datos del formulario antes de la creación.
     * 
     * @param array $datos Datos del formulario.
     * @return array Datos modificados.
     */
    protected function mutateFormDataBeforeCreate(array $datos): array
    {
        if (auth()->user()->isAlumno()) {
            $datos['alumno_id'] = auth()->user()->alumno?->id;
        }

        return $datos;
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
