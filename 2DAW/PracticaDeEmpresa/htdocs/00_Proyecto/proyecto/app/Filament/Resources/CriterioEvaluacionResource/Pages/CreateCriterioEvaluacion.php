<?php

namespace App\Filament\Resources\CriterioEvaluacionResource\Pages;

use App\Filament\Resources\CriterioEvaluacionResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * @class CreateCriterioEvaluacion
 * @brief Página para la creación de Criterios de Evaluación.
 */
use Filament\Notifications\Notification;

class CreateCriterioEvaluacion extends CreateRecord
{
    protected static string $resource = CriterioEvaluacionResource::class;

    protected function afterCreate(): void
    {
        Notification::make()
            ->success()
            ->title('Criterio registrado')
            ->body("El criterio de evaluación ha sido registrado correctamente.")
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
