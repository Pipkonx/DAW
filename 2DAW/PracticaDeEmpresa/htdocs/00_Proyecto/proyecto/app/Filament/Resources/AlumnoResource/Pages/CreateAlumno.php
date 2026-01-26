<?php

namespace App\Filament\Resources\AlumnoResource\Pages;

use App\Filament\Resources\AlumnoResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * @class CreateAlumno
 * @brief Página para la creación de registros de Alumnos.
 */
class CreateAlumno extends CreateRecord
{
    protected static string $resource = AlumnoResource::class;

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
