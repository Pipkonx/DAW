<?php

namespace App\Filament\Resources\AlumnoResource\Pages;

use App\Filament\Resources\AlumnoResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * @class CreateAlumno
 * @brief Página para la creación de registros de Alumnos.
 */
use Filament\Notifications\Notification;

class CreateAlumno extends CreateRecord
{
    protected static string $resource = AlumnoResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Alumno registrado')
            ->body("El alumno " . ($this->record->user?->name ?? 'desconocido') . " ha sido registrado correctamente.")
            ->sendToDatabase(\Filament\Facades\Filament::auth()->user());
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
