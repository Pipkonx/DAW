<?php

namespace App\Filament\Resources\AlumnoResource\Pages;

use App\Filament\Resources\AlumnoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * @class EditAlumno
 * @brief Página para la edición de registros de Alumnos.
 */
use Filament\Notifications\Notification;

class EditAlumno extends EditRecord
{
    protected static string $resource = AlumnoResource::class;

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Alumno actualizado')
            ->body("Los datos del alumno " . ($this->record->user?->name ?? 'desconocido') . " han sido actualizados.")
            ->sendToDatabase(\Filament\Facades\Filament::auth()->user());
    }

    /**
     * @brief Define las acciones de la cabecera en la página de edición.
     * 
     * @return array Lista de acciones disponibles.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @brief Obtiene la URL de redirección tras editar un registro.
     * 
     * @return string URL de la página de listado (Index).
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
