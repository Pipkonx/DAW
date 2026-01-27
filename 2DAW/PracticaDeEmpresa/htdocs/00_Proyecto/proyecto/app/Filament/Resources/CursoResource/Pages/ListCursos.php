<?php

namespace App\Filament\Resources\CursoResource\Pages;

use App\Filament\Resources\CursoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Notifications\Notification;
use Filament\Tables;

class ListCursos extends ListRecords
{
    protected static string $resource = CursoResource::class;

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
                        ->title('Curso eliminado')
                        ->body("El curso {$record->nombre} ha sido eliminado del sistema.")
                        ->sendToDatabase(\Filament\Facades\Filament::auth()->user())
                        ->send();
                }),
        ];
    }
}
