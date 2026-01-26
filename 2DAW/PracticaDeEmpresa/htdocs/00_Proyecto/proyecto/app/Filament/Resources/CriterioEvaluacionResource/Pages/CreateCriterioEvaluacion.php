<?php

namespace App\Filament\Resources\CriterioEvaluacionResource\Pages;

use App\Filament\Resources\CriterioEvaluacionResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * @class CreateCriterioEvaluacion
 * @brief Página para la creación de Criterios de Evaluación.
 */
class CreateCriterioEvaluacion extends CreateRecord
{
    protected static string $resource = CriterioEvaluacionResource::class;

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
