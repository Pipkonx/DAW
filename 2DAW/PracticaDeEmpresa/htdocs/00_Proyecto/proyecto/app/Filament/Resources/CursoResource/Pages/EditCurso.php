<?php

namespace App\Filament\Resources\CursoResource\Pages;

use App\Filament\Resources\CursoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

use Filament\Notifications\Notification;

/**
 * @class EditCurso
 * @brief Página para la edición de registros de Cursos.
 */
class EditCurso extends EditRecord
{
    protected static string $resource = CursoResource::class;

    protected function afterSave(): void
    {
        Notification::make()
            ->success()
            ->title('Curso actualizado')
            ->body("Los datos del curso {$this->record->nombre} han sido actualizados.")
            ->sendToDatabase(\Filament\Facades\Filament::auth()->user())
            ->send();
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
