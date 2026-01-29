<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Filament\Tables;

/**
 * @class ListUsers
 * @brief Página para el listado de registros de Usuarios.
 */
class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    /**
     * @brief Define las acciones de la cabecera en la página de listado.
     * 
     * @return array Lista de acciones disponibles.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('managePermissions')
                ->label('Gestionar Permisos (Estilo Discord)')
                ->icon('heroicon-o-shield-check')
                ->color('warning')
                ->url(fn (): string => static::getResource()::getUrl('permissions')),
            Actions\CreateAction::make(),
        ];
    }
}
