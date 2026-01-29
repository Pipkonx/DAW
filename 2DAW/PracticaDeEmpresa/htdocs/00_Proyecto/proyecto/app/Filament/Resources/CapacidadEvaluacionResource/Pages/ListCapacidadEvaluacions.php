<?php

namespace App\Filament\Resources\CapacidadEvaluacionResource\Pages;

use App\Filament\Resources\CapacidadEvaluacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Notifications\Notification;
use Filament\Tables;

/**
 * @class ListCapacidadEvaluacions
 * @brief Página para el listado de Capacidades de Evaluación.
 */
class ListCapacidadEvaluacions extends ListRecords
{
    protected static string $resource = CapacidadEvaluacionResource::class;

    /**
     * @brief Define las acciones de la cabecera en la página de listado.
     * 
     * @return array Lista de acciones disponibles.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Capacidad'),
        ];
    }
}
