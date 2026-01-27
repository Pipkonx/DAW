<?php

namespace App\Filament\Resources\AlumnoResource\Pages;

use App\Filament\Resources\AlumnoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Notifications\Notification;
use Filament\Tables;

class ListAlumnos extends ListRecords
{
    protected static string $resource = AlumnoResource::class;

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
                        ->title('Alumno eliminado')
                        ->body("El alumno {$record->user->name} ha sido eliminado del sistema.")
                        ->sendToDatabase(\Filament\Facades\Filament::auth()->user())
                        ->send();
                }),
        ];
    }
}
