<?php

namespace App\Filament\Resources\EmpresaResource\Pages;

use App\Filament\Resources\EmpresaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Notifications\Notification;
use Filament\Tables;

/**
 * @class ListEmpresas
 * @brief Página para el listado de registros de Empresas.
 */
class ListEmpresas extends ListRecords
{
    protected static string $resource = EmpresaResource::class;

    /**
     * @brief Define las acciones de la cabecera en la página de listado.
     * 
     * @return array Lista de acciones disponibles.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Empresa'),
        ];
    }
}
