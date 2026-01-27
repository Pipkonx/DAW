<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Alumno;
use App\Models\TutorCurso;
use App\Models\TutorPracticas;
use Filament\Notifications\Notification;

/**
 * @class EditUser
 * @brief Página para la edición de registros de Usuarios.
 */
class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Usuario actualizado')
            ->body("Los datos del usuario {$this->record->name} han sido actualizados.")
            ->sendToDatabase(\Filament\Facades\Filament::auth()->user());
    }

    /**
     * @brief Lógica ejecutada después de guardar el usuario.
     */
    protected function afterSave(): void
    {
        // Actualizar perfil relacionado (lógica original)
        $data = $this->form->getRawState();
        $usuario = $this->record;
        $rol = $data['rol'] ?? null;
        $datosPerfil = $data['datosPerfil'] ?? [];

        if ($rol && $rol !== 'admin' && !empty($datosPerfil)) {
            $this->actualizarPerfilRelacionado($usuario, $rol, $datosPerfil);
        }
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

    /**
     * @brief Prepara los datos del formulario antes de cargarlos en la página de edición.
     * 
     * @param array $data Datos del registro.
     * @return array Datos formateados para el formulario.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $usuario = $this->record;
        $rol = $usuario->getRoleNames()->first();
        $data['rol'] = $rol;

        // Cargar datos del perfil según el rol y reference_id
        if ($recordId = $this->record->reference_id) {
            $perfil = match ($rol) {
                'alumno' => \App\Models\Alumno::find($recordId),
                'tutor_curso' => \App\Models\TutorCurso::find($recordId),
                'tutor_practicas' => \App\Models\TutorPracticas::find($recordId),
                'empresa' => \App\Models\Empresa::find($recordId),
                default => null,
            };

            if ($perfil) {
                $data['datosPerfil'] = $perfil->toArray();
            }
        }

        return $data;
    }

    /**
     * @brief Actualiza o crea el perfil relacionado según el rol.
     */
    private function actualizarPerfilRelacionado($usuario, $rol, $datosPerfil): void
    {
        $recordId = $usuario->reference_id;

        $perfil = match ($rol) {
            'alumno' => Alumno::updateOrCreate(['id' => $recordId], $datosPerfil),
            'tutor_curso' => TutorCurso::updateOrCreate(['id' => $recordId], $datosPerfil),
            'tutor_practicas' => TutorPracticas::updateOrCreate(['id' => $recordId], $datosPerfil),
            default => null,
        };

        if ($perfil && !$recordId) {
            $usuario->update(['reference_id' => $perfil->id]);
            $perfil->update(['user_id' => $usuario->id]);
        }
    }
}