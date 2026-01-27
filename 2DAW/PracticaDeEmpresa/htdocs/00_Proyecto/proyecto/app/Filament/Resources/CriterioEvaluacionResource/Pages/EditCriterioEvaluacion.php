<?php

namespace App\Filament\Resources\CriterioEvaluacionResource\Pages;

use App\Filament\Resources\CriterioEvaluacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * @class EditCriterioEvaluacion
 * @brief Página para la edición de Criterios de Evaluación.
 */
use Filament\Notifications\Notification;

class EditCriterioEvaluacion extends EditRecord
{
    protected static string $resource = CriterioEvaluacionResource::class;

    protected function afterSave(): void
    {
        Notification::make()
            ->success()
            ->title('Criterio actualizado')
            ->body("Los datos del criterio han sido actualizados.")
            ->sendToDatabase(\Filament\Facades\Filament::auth()->user())
            ->send();
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
