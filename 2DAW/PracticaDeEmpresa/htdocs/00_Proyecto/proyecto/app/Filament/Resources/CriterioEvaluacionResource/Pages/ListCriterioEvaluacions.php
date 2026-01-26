<?php

namespace App\Filament\Resources\CriterioEvaluacionResource\Pages;

use App\Filament\Resources\CriterioEvaluacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCriterioEvaluacions extends ListRecords
{
    protected static string $resource = CriterioEvaluacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
