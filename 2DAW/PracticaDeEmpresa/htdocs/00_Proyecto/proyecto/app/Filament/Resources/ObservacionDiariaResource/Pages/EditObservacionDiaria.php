<?php

namespace App\Filament\Resources\ObservacionDiariaResource\Pages;

use App\Filament\Resources\ObservacionDiariaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * @class EditObservacionDiaria
 * @brief Página para la edición de Observaciones Diarias.
 */
class EditObservacionDiaria extends EditRecord
{
    protected static string $resource = ObservacionDiariaResource::class;

    /**
     * @brief Define las acciones de la cabecera en la página de edición.
     * 
     * @return array Lista de acciones disponibles.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * @brief Obtiene la URL de redirección tras editar un registro.
     * 
     * @return string URL de la página de listado (Index).
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
