<?php

namespace App\Filament\Resources\EvaluacionResource\Pages;

use App\Filament\Resources\EvaluacionResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * @class CreateEvaluacion
 * @brief Página para la creación de Evaluaciones.
 */
use Filament\Notifications\Notification;

class CreateEvaluacion extends CreateRecord
{
    protected static string $resource = EvaluacionResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Evaluación registrada')
            ->body("Se ha registrado una nueva evaluación para el alumno " . ($this->record->alumno?->user?->name ?? 'desconocido') . ".")
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

    /**
     * @brief Modifica los datos antes de la creación del registro.
     * 
     * Calcula la nota final basándose en el promedio de las notas de los detalles.
     * 
     * @param array $data Datos del formulario.
     * @return array Datos modificados.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['detalles']) && count($data['detalles']) > 0) {
            $suma = collect($data['detalles'])->sum('nota');
            $data['nota_final'] = $suma / count($data['detalles']);
        }

        return $data;
    }
}
