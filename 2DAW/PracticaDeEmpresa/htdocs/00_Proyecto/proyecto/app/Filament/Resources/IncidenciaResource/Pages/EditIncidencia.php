<?php

namespace App\Filament\Resources\IncidenciaResource\Pages;

use App\Filament\Resources\IncidenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * @class EditIncidencia
 * @brief Página para la edición de registros de Incidencias.
 */
class EditIncidencia extends EditRecord
{
    protected static string $resource = IncidenciaResource::class;

    /**
     * @brief Define las acciones de la cabecera en la página de edición.
     * 
     * @return array Lista de acciones disponibles.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @brief Obtiene la URL de redirección tras editar un registro.
     * 
     * @return string URL de la página de listado (Index).
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
