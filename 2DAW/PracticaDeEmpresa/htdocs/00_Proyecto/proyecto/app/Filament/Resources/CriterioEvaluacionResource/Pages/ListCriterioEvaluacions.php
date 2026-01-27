<?php

namespace App\Filament\Resources\CriterioEvaluacionResource\Pages;

use App\Filament\Resources\CriterioEvaluacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Notifications\Notification;
use Filament\Tables;

class ListCriterioEvaluacions extends ListRecords
{
    protected static string $resource = CriterioEvaluacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\DeleteAction::make()
                ->after(function ($record) {
                    Notification::make()
                        ->warning()
                        ->title('Criterio eliminado')
                        ->body("Se ha eliminado el criterio de evaluación.")
                        ->sendToDatabase(auth()->user());
                }),
        ];
    }
}
