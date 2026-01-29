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

    /**
     * @brief Obtiene la notificación de éxito al guardar un usuario.
     * 
     * @return Notification|null Objeto de notificación configurado.
     */
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Usuario actualizado')
            ->body("Los datos del usuario {$this->record->name} han sido actualizados.")
            ->sendToDatabase(\Filament\Facades\Filament::auth()->user());
    }

    /**
     * @brief Sobrescribe el proceso de actualización para gestionar roles y perfiles.
     * 
     * @param \Illuminate\Database\Eloquent\Model $record Instancia del registro a actualizar.
     * @param array $data Datos del formulario.
     * @return \Illuminate\Database\Eloquent\Model Registro actualizado.
     */
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($record, $data) {
            $rol = $data['rol'] ?? null;
            $datosPerfil = $data['datosPerfil'] ?? [];

            // 1. Actualizar datos básicos del usuario
            $datosUsuario = [
                'name' => $data['name'],
                'email' => $data['email'],
                'avatar_url' => $data['avatar_url'] ?? $record->avatar_url,
            ];

            if (!empty($data['password'])) {
                $datosUsuario['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
            }

            $record->update($datosUsuario);

            // 2. Sincronizar el rol
            if ($rol) {
                $record->syncRoles([$rol]);
            }

            // 3. Gestionar el perfil relacionado
            if ($rol && $rol !== 'admin') {
                $this->actualizarPerfilRelacionado($record, $rol, $datosPerfil);
            }

            return $record;
        });
    }

    /**
     * @brief Lógica ejecutada después de guardar el usuario.
     */
    protected function afterSave(): void
    {
        // La lógica se ha movido a handleRecordUpdate para mayor consistencia
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

        // Cargar datos del perfil según el rol
        $perfil = match ($rol) {
            'alumno' => $usuario->alumno,
            'tutor_practicas' => $usuario->perfilTutorPracticas,
            'tutor_curso' => $usuario->perfilTutorCurso,
            'empresa' => $usuario->empresa,
            default => null,
        };

        if ($perfil) {
            $data['datosPerfil'] = $perfil->toArray();
        }

        return $data;
    }

    /**
     * @brief Actualiza o crea el perfil relacionado del usuario.
     * 
     * @param \App\Models\User $usuario Instancia del usuario.
     * @param string $rol Nombre del rol.
     * @param array $datosPerfil Datos del perfil.
     * @return void
     */
    protected function actualizarPerfilRelacionado($usuario, $rol, $datosPerfil): void
    {
        $perfil = match ($rol) {
            'alumno' => $usuario->alumno(),
            'tutor_practicas' => $usuario->perfilTutorPracticas(),
            'tutor_curso' => $usuario->perfilTutorCurso(),
            'empresa' => $usuario->empresa(),
            default => null,
        };

        if ($perfil) {
            $perfil->updateOrCreate(['user_id' => $usuario->id], $datosPerfil);
        }
    }
}