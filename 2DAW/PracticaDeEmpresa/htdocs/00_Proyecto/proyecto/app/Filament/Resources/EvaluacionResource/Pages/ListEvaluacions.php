<?php

namespace App\Filament\Resources\EvaluacionResource\Pages;

use App\Filament\Resources\EvaluacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Notifications\Notification;
use Filament\Tables;

/**
 * @class ListEvaluacions
 * @brief Página para el listado de Evaluaciones.
 */
class ListEvaluacions extends ListRecords
{
    protected static string $resource = EvaluacionResource::class;

    /**
     * @brief Define las acciones de la cabecera en la página de listado.
     * 
     * @return array Lista de acciones disponibles.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Evaluación'),
        ];
    }
}
