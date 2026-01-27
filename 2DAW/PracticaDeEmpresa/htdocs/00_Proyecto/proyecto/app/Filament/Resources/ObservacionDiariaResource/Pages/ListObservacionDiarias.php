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

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\DeleteAction::make()
                ->after(function ($record) {
                    Notification::make()
                        ->warning()
                        ->title('Observación diaria eliminada')
                        ->body("Se ha eliminado la observación del alumno {$record->alumno->user->name}.")
                        ->sendToDatabase(auth()->user());
                }),
        ];
    }
}
