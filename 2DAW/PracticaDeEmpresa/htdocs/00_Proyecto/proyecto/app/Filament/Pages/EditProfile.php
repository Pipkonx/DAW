<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

/**
 * @class EditProfile
 * @brief Página personalizada para que los usuarios editen su perfil.
 * 
 * Permite a los usuarios actualizar su información básica, cambiar su contraseña
 * y subir una foto de perfil (avatar). También incluye campos específicos según
 * el rol del usuario (Alumno, Tutor, etc.).
 */
class EditProfile extends BaseEditProfile
{
    protected static string $layout = 'filament-panels::components.layout.index';

    /**
     * @brief Define el formulario de edición de perfil.
     * 
     * @param Form $form Objeto de formulario de Filament.
     * @return Form Formulario configurado con campos de usuario y perfil.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Información de Usuario')
                    ->description('Datos básicos de acceso y contacto.')
                    ->schema([
                        $this->getNameFormComponent()
                            ->disabled(!auth()->user()->isAdmin()),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])->columns(2),

                Section::make('Foto de Perfil')
                    ->description('Sube una imagen para tu avatar. Se guardará en el sistema de archivos.')
                    ->schema([
                        FileUpload::make('avatar_url')
                            ->label('Avatar')
                            ->image()
                            ->disk('public')
                            ->directory('avatars')
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper(),
                    ]),

                Section::make('Información Adicional')
                    ->description('Campos específicos según tu rol en el sistema.')
                    ->schema($this->getAdditionalFields())
                    ->columns(2),
            ]);
    }

    /**
     * @brief Obtiene los campos adicionales dinámicamente según el rol del usuario.
     * 
     * @return array Lista de componentes de formulario.
     */
    protected function getAdditionalFields(): array
    {
        $user = auth()->user();
        $fields = [];

        if ($user->isAlumno()) {
            $fields = [
                TextInput::make('alumno_dni')
                    ->label('DNI')
                    ->default($user->alumno?->dni)
                    ->disabled(), // El DNI no suele ser editable por el alumno
                TextInput::make('alumno_telefono')
                    ->label('Teléfono')
                    ->default($user->alumno?->telefono)
                    ->tel(),
            ];
        } elseif ($user->isTutorCurso()) {
            $fields = [
                TextInput::make('tutor_curso_dni')
                    ->label('DNI')
                    ->default($user->perfilTutorCurso?->dni)
                    ->disabled(),
                TextInput::make('tutor_curso_telefono')
                    ->label('Teléfono')
                    ->default($user->perfilTutorCurso?->telefono)
                    ->tel(),
                TextInput::make('tutor_curso_departamento')
                    ->label('Departamento')
                    ->default($user->perfilTutorCurso?->departamento),
            ];
        } elseif ($user->isTutorPracticas()) {
            $fields = [
                TextInput::make('tutor_empresa_dni')
                    ->label('DNI')
                    ->default($user->perfilTutorPracticas?->dni)
                    ->disabled(),
                TextInput::make('tutor_empresa_telefono')
                    ->label('Teléfono')
                    ->default($user->perfilTutorPracticas?->telefono)
                    ->tel(),
            ];
        }

        return $fields;
    }

    /**
     * @brief Procesa los datos antes de guardar el modelo User.
     * 
     * Se encarga de actualizar los modelos de perfil relacionados (Alumno, Tutor, etc.)
     * antes de que Filament guarde los datos del usuario principal.
     * 
     * @param array $data Datos del formulario.
     * @return array Datos limpios para el modelo User.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = auth()->user();

        // Actualizar datos del perfil relacionado
        if ($user->isAlumno()) {
            $user->alumno()->update([
                'telefono' => $data['alumno_telefono'] ?? $user->alumno?->telefono,
            ]);
        } elseif ($user->isTutorCurso()) {
            $user->perfilTutorCurso()->update([
                'telefono' => $data['tutor_curso_telefono'] ?? $user->perfilTutorCurso?->telefono,
                'departamento' => $data['tutor_curso_departamento'] ?? $user->perfilTutorCurso?->departamento,
            ]);
        } elseif ($user->isTutorEmpresa()) {
            $user->perfilTutorPracticas()->update([
                'telefono' => $data['tutor_empresa_telefono'] ?? $user->perfilTutorPracticas?->telefono,
                'puesto' => $data['tutor_empresa_puesto'] ?? $user->perfilTutorPracticas?->puesto,
            ]);
        }

        // Enviar notificación a la base de datos
        Notification::make()
            ->success()
            ->title('Perfil actualizado')
            ->body('Tus cambios han sido guardados correctamente.')
            ->sendToDatabase($user);

        // Limpiar el array de datos para que solo contenga campos del modelo User
        unset(
            $data['alumno_telefono'], 
            $data['tutor_curso_telefono'], 
            $data['tutor_curso_departamento'],
            $data['tutor_empresa_telefono'],
            $data['tutor_empresa_puesto'],
            $data['alumno_dni'], 
            $data['tutor_curso_dni'], 
            $data['tutor_empresa_dni']
        );

        return $data;
    }
}
