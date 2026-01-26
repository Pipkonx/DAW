<?php

namespace App\Filament\Resources\ObservacionDiariaResource\Pages;

use App\Filament\Resources\ObservacionDiariaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListObservacionDiarias extends ListRecords
{
    protected static string $resource = ObservacionDiariaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
