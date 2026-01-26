<?php

namespace App\Filament\Resources\IncidenciaResource\Pages;

use App\Filament\Resources\IncidenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIncidencias extends ListRecords
{
    protected static string $resource = IncidenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
