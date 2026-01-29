<?php

namespace App\Filament\Resources\IncidenciaResource\Pages;

use App\Filament\Resources\IncidenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * @class ListIncidencias
 * @brief Página para el listado de registros de Incidencias.
 */
class ListIncidencias extends ListRecords
{
    protected static string $resource = IncidenciaResource::class;

    /**
     * @brief Define las acciones de la cabecera en la página de listado.
     * 
     * @return array Lista de acciones disponibles.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Incidencia'),
        ];
    }
}
