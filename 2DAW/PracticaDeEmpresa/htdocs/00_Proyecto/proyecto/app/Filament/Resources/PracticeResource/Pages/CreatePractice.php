<?php

namespace App\Filament\Resources\PracticeResource\Pages;

use App\Filament\Resources\PracticeResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePractice extends CreateRecord
{
    protected static string $resource = PracticeResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
