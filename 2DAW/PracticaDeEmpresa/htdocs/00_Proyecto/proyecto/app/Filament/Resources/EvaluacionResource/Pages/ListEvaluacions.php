<?php

namespace App\Filament\Resources\EvaluacionResource\Pages;

use App\Filament\Resources\EvaluacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEvaluacions extends ListRecords
{
    protected static string $resource = EvaluacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
