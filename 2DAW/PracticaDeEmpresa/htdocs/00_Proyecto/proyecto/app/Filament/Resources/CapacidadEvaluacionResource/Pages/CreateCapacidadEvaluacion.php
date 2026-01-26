<?php

namespace App\Filament\Resources\CapacidadEvaluacionResource\Pages;

use App\Filament\Resources\CapacidadEvaluacionResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * @class CreateCapacidadEvaluacion
 * @brief Página para la creación de Capacidades de Evaluación.
 */
class CreateCapacidadEvaluacion extends CreateRecord
{
    protected static string $resource = CapacidadEvaluacionResource::class;

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
