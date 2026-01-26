<?php

namespace App\Filament\Resources\CapacidadEvaluacionResource\Pages;

use App\Filament\Resources\CapacidadEvaluacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCapacidadEvaluacions extends ListRecords
{
    protected static string $resource = CapacidadEvaluacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
