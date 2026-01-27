<?php

namespace App\Filament\Resources\EmpresaResource\Pages;

use App\Filament\Resources\EmpresaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Filament\Notifications\Notification;

/**
 * @class EditEmpresa
 * @brief Página para la edición de registros de Empresas.
 */
class EditEmpresa extends EditRecord
{
    protected static string $resource = EmpresaResource::class;

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Empresa actualizada')
            ->body("Los datos de la empresa {$this->record->nombre} han sido actualizados.")
            ->sendToDatabase(\Filament\Facades\Filament::auth()->user());
    }

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
