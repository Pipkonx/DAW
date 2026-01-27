<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Alumno;
use App\Models\TutorCurso;
use App\Models\TutorPracticas;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Filament\Notifications\Notification;

/**
 * @class CreateUser
 * @brief Página para la creación de usuarios y sus perfiles relacionados.
 */
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Usuario creado')
            ->body("El usuario {$this->record->name} ha sido creado correctamente.")
            ->sendToDatabase(\Filament\Facades\Filament::auth()->user());
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * @brief Sobrescribe el proceso de creación para gestionar perfiles y reference_id.
     * 
     * @param array $data Datos del formulario.
     * @return Model Instancia del usuario creado.
     */
    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $rol = $data['rol'] ?? null;
            $datosPerfil = $data['datosPerfil'] ?? [];
            $referenceId = null;

            // 1. Crear el perfil específico primero si no es admin
            if ($rol && $rol !== 'admin') {
                $perfil = match ($rol) {
                    'alumno' => \App\Models\Alumno::create($datosPerfil),
                    'tutor_curso' => \App\Models\TutorCurso::create($datosPerfil),
                    'tutor_practicas' => \App\Models\TutorPracticas::create($datosPerfil),
                    'empresa' => \App\Models\Empresa::create($datosPerfil),
                    default => null,
                };

                if ($perfil) {
                    $referenceId = $perfil->id;
                }
            }

            // 2. Crear el usuario con el reference_id
            $usuario = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'reference_id' => $referenceId,
            ]);

            // 3. Asignar el rol
            if ($rol) {
                $usuario->assignRole($rol);
            }

            // 4. Vincular el perfil con el user_id para consistencia
            if (isset($perfil) && $perfil) {
                $perfil->update(['user_id' => $usuario->id]);
            }

            return $usuario;
        });
    }
}
