<?php

namespace App\Filament\Resources\CapacidadEvaluacionResource\Pages;

use App\Filament\Resources\CapacidadEvaluacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * @class EditCapacidadEvaluacion
 * @brief Página para la edición de Capacidades de Evaluación.
 */
use Filament\Notifications\Notification;

class EditCapacidadEvaluacion extends EditRecord
{
    protected static string $resource = CapacidadEvaluacionResource::class;

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Capacidad actualizada')
            ->body("Los datos de la capacidad han sido actualizados.")
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
            Actions\ViewAction::make(),
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
