<?php

namespace App\Filament\Resources\EmpresaResource\Pages;

use App\Filament\Resources\EmpresaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

use Filament\Notifications\Notification;
use Filament\Tables;

class ListEmpresas extends ListRecords
{
    protected static string $resource = EmpresaResource::class;

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
                        ->title('Empresa eliminada')
                        ->body("La empresa {$record->nombre} ha sido eliminada del sistema.")
                        ->sendToDatabase(\Filament\Facades\Filament::auth()->user())
                        ->send();
                }),
        ];
    }
}
