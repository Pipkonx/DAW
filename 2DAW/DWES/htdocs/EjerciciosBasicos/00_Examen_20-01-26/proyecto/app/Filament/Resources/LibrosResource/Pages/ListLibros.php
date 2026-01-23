<?php

namespace App\Filament\Resources\LibrosResource\Pages;

use App\Filament\Resources\LibrosResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLibros extends ListRecords
{
    protected static string $resource = LibrosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
