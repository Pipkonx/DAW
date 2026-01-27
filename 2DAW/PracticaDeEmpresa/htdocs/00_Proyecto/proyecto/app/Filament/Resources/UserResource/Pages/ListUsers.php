<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Filament\Tables;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

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
                        ->title('Usuario eliminado')
                        ->body("El usuario {$record->name} ha sido eliminado del sistema.")
                        ->sendToDatabase(\Filament\Facades\Filament::auth()->user())
                        ->send();
                }),
        ];
    }
}
