<?php

namespace App\Filament\Resources\CapacidadEvaluacionResource\Pages;

use App\Filament\Resources\CapacidadEvaluacionResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * @class CreateCapacidadEvaluacion
 * @brief Página para la creación de Capacidades de Evaluación.
 */
use Filament\Notifications\Notification;

class CreateCapacidadEvaluacion extends CreateRecord
{
    protected static string $resource = CapacidadEvaluacionResource::class;

    protected function afterCreate(): void
    {
        Notification::make()
            ->success()
            ->title('Capacidad registrada')
            ->body("La capacidad de evaluación ha sido registrada correctamente.")
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
