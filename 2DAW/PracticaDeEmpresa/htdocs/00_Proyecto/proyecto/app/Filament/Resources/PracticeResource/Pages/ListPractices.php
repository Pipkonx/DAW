<?php

namespace App\Filament\Resources\PracticeResource\Pages;

use App\Filament\Resources\PracticeResource;
use App\Models\Practice;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Form;

class ListPractices extends ListRecords
{
    protected static string $resource = PracticeResource::class;

    public function form(Form $form): Form
    {
        return PracticeResource::form($form);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
