<?php

namespace App\Filament\Resources\CursoResource\Pages;

use App\Filament\Resources\CursoResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * @class CreateCurso
 * @brief Página para la creación de registros de Cursos.
 */
class CreateCurso extends CreateRecord
{
    protected static string $resource = CursoResource::class;

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
