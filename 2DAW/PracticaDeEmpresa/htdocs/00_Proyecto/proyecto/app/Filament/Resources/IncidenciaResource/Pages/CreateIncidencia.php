<?php

namespace App\Filament\Resources\IncidenciaResource\Pages;

use App\Filament\Resources\IncidenciaResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * @class CreateIncidencia
 * @brief Página para la creación de registros de Incidencias.
 */
class CreateIncidencia extends CreateRecord
{
    protected static string $resource = IncidenciaResource::class;

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
