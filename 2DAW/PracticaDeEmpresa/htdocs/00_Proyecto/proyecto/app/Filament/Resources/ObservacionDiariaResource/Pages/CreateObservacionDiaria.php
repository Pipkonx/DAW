<?php

namespace App\Filament\Resources\ObservacionDiariaResource\Pages;

use App\Filament\Resources\ObservacionDiariaResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * @class CreateObservacionDiaria
 * @brief Página para la creación de Observaciones Diarias.
 */
class CreateObservacionDiaria extends CreateRecord
{
    protected static string $resource = ObservacionDiariaResource::class;

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
