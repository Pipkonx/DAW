<?php

namespace App\Filament\Resources\EvaluacionResource\Pages;

use App\Filament\Resources\EvaluacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * @class EditEvaluacion
 * @brief Página para la edición de Evaluaciones.
 */
use Filament\Notifications\Notification;

class EditEvaluacion extends EditRecord
{
    protected static string $resource = EvaluacionResource::class;

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Evaluación actualizada')
            ->body("Los datos de la evaluación han sido actualizados.")
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

    /**
     * @brief Modifica los datos antes de guardar el registro.
     * 
     * Calcula la nota final basándose en el promedio de las notas de los detalles.
     * 
     * @param array $data Datos del formulario.
     * @return array Datos modificados.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['detalles']) && count($data['detalles']) > 0) {
            $suma = collect($data['detalles'])->sum('nota');
            $data['nota_final'] = $suma / count($data['detalles']);
        }

        return $data;
    }
}
