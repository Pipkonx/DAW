<?php

namespace App\Filament\Resources\EmpresaResource\Pages;

use App\Filament\Resources\EmpresaResource;
use Filament\Resources\Pages\CreateRecord;

use Filament\Notifications\Notification;

/**
 * @class CreateEmpresa
 * @brief Página para la creación de registros de Empresas.
 */
class CreateEmpresa extends CreateRecord
{
    protected static string $resource = EmpresaResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Empresa registrada')
            ->body("La empresa {$this->record->nombre} ha sido registrada correctamente.")
            ->sendToDatabase(\Filament\Facades\Filament::auth()->user());
    }

    /**
     * @brief Obtiene la URL de redirección tras crear un registro.
     * 
     * @return string URL de la página de listado (Index).
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * @brief Modifica los datos del formulario antes de la creación del registro.
     * Asigna automáticamente la fecha de creación actual.
     * 
     * @param array $data Datos provenientes del formulario.
     * @return array Datos modificados con la fecha de creación.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['fecha_creacion'] = now();
        return $data;
    }
}
