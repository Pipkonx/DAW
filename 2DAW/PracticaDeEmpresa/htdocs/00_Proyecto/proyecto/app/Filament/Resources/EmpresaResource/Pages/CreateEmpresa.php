<?php

namespace App\Filament\Resources\EmpresaResource\Pages;

use App\Filament\Resources\EmpresaResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * @class CreateEmpresa
 * @brief Página para la creación de registros de Empresas.
 */
class CreateEmpresa extends CreateRecord
{
    protected static string $resource = EmpresaResource::class;

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
     * @brief Modifica los datos del formulario antes de la creación del registro.
     * Asigna automáticamente la fecha de creación actual.
     * 
     * @param array $datos Datos provenientes del formulario.
     * @return array Datos modificados con la fecha de creación.
     */
    protected function mutateFormDataBeforeCreate(array $datos): array
    {
        $datos['fecha_creacion'] = now();
        return $datos;
    }
}
