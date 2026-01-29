<?php

namespace App\Filament\Resources\CriterioEvaluacionResource\Pages;

use App\Filament\Resources\CriterioEvaluacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

/**
 * @class ViewCriterioEvaluacion
 * @brief Página para visualizar un Criterio de Evaluación.
 */
class ViewCriterioEvaluacion extends ViewRecord
{
    protected static string $resource = CriterioEvaluacionResource::class;

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
