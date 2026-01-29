<?php

namespace App\Filament\Resources\AlumnoResource\Pages;

use App\Filament\Resources\AlumnoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Notifications\Notification;
use Filament\Tables;

/**
 * @class ListAlumnos
 * @brief Página para el listado de registros de Alumnos.
 */
class ListAlumnos extends ListRecords
{
    protected static string $resource = AlumnoResource::class;

    /**
     * @brief Define las acciones de la cabecera en la página de listado.
     * 
     * @return array Lista de acciones disponibles.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Alumno'),
        ];
    }
}
