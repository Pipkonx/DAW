<?php

namespace App\Filament\Resources\CapacidadEvaluacionResource\Pages;

use App\Filament\Resources\CapacidadEvaluacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

/**
 * @class ViewCapacidadEvaluacion
 * @brief Página para visualizar una Capacidad de Evaluación.
 */
class ViewCapacidadEvaluacion extends ViewRecord
{
    protected static string $resource = CapacidadEvaluacionResource::class;

    /**
     * @brief Define las acciones de la cabecera en la página de visualización.
     * 
     * @return array Lista de acciones disponibles.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
