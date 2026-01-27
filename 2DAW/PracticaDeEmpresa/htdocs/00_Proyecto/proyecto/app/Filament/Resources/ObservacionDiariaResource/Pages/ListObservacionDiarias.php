<?php

namespace App\Filament\Resources\ObservacionDiariaResource\Pages;

use App\Filament\Resources\ObservacionDiariaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Notifications\Notification;
use Filament\Tables;

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
