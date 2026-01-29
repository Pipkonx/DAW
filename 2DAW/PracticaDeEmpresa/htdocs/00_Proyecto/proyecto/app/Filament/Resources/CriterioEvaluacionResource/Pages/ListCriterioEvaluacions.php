<?php

namespace App\Filament\Resources\CriterioEvaluacionResource\Pages;

use App\Filament\Resources\CriterioEvaluacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Notifications\Notification;
use Filament\Tables;

/**
 * @class ListCriterioEvaluacions
 * @brief Página para el listado de Criterios de Evaluación.
 */
class ListCriterioEvaluacions extends ListRecords
{
    protected static string $resource = CriterioEvaluacionResource::class;

    /**
     * @brief Define las acciones de la cabecera en la página de listado.
     * 
     * @return array Lista de acciones disponibles.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Criterio'),
        ];
    }
}
