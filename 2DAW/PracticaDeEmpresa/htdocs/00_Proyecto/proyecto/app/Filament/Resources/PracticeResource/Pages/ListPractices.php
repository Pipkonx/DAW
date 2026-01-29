<?php

namespace App\Filament\Resources\PracticeResource\Pages;

use App\Filament\Resources\PracticeResource;
use App\Models\Practice;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Form;

/**
 * @class ListPractices
 * @brief Página para el listado de registros de Prácticas.
 */
class ListPractices extends ListRecords
{
    protected static string $resource = PracticeResource::class;

    /**
     * @brief Obtiene el formulario para la página de listado (si se requiere).
     * 
     * @param Form $formulario Objeto del formulario.
     * @return Form Formulario configurado.
     */
    public function form(Form $formulario): Form
    {
        return PracticeResource::form($formulario);
    }

    /**
     * @brief Define las acciones de la cabecera en la página de listado.
     * 
     * @return array Lista de acciones disponibles.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Práctica')
                ->modal(),
        ];
    }
}
