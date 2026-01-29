<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

use Filament\Tables\Actions\ActionGroup;

/**
 * @class UserResource
 * @brief Recurso de Filament para la gestión de usuarios y sus perfiles relacionados.
 */
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Usuario';

    protected static ?string $pluralModelLabel = 'Usuarios';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    /**
     * @brief Determina si el recurso debe registrarse en la navegación.
     * 
     * @return bool True si el usuario es administrador.
     */
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->hasPermissionTo('gestionar_usuarios');
    }

    /**
     * @brief Determina si el usuario puede ver cualquier registro.
     * 
     * @return bool True si el usuario es administrador.
     */
    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->hasPermissionTo('gestionar_usuarios');
    }

    /**
     * @brief Determina si el usuario puede crear nuevos registros.
     * 
     * @return bool True si el usuario es administrador.
     */
    public static function canCreate(): bool
    {
        return auth()->user()->isAdmin();
    }

    /**
     * @brief Determina si el usuario puede editar un registro específico.
     * 
     * @param \Illuminate\Database\Eloquent\Model $record Instancia del registro a editar.
     * @return bool True si el usuario es administrador.
     */
    public static function canEdit($record): bool
    {
        return auth()->user()->isAdmin();
    }

    /**
     * @brief Determina si el usuario puede eliminar un registro específico.
     * 
     * @param \Illuminate\Database\Eloquent\Model $record Instancia del registro a eliminar.
     * @return bool True si el usuario es administrador.
     */
    public static function canDelete($record): bool
    {
        return auth()->user()->isAdmin();
    }

    /**
     * @brief Obtiene la consulta base optimizada para el recurso Usuario.
     * 
     * @return \Illuminate\Database\Eloquent\Builder Consulta configurada con carga ansiosa y filtros por rol.
     */
    public static function getEloquentQuery(): Builder
    {
        $consulta = parent::getEloquentQuery()
            ->with(['roles', 'alumno.curso', 'perfilTutorPracticas.empresa', 'empresa', 'perfilTutorCurso']);
        $usuarioActual = auth()->user();

        if ($usuarioActual->isAdmin()) {
            return $consulta;
        }

        if ($usuarioActual->isTutorPracticas()) {
            return $consulta->role('alumno');
        }

        return $consulta->where('id', $usuarioActual->id);
    }

    /**
     * @brief Obtiene el formulario configurado para el recurso Usuario.
     * 
     * @param Form $formulario Objeto del formulario.
     * @return Form Formulario dinámico basado en el rol seleccionado.
     */
    public static function form(Form $formulario): Form
    {
        return $formulario
            ->schema([
                Section::make('Información Básica')
                    ->description('Datos principales de acceso y perfil.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre Completo')
                            ->placeholder('Ej: Juan Pérez')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->placeholder('correo@ejemplo.com')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->placeholder('Mínimo 8 caracteres')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->revealable(),
                        Select::make('rol')
                            ->label('Rol del Usuario')
                            ->placeholder('Selecciona un rol')
                            ->options(function () {
                                if (auth()->user()->isAdmin()) {
                                    return [
                                        'admin' => 'Administrador',
                                        'alumno' => 'Alumno',
                                        'tutor_practicas' => 'Tutor de Empresa',
                                        'tutor_curso' => 'Tutor de Centro',
                                        'empresa' => 'Empresa',
                                    ];
                                }
                                if (auth()->user()->isTutorPracticas()) {
                                    return [
                                        'alumno' => 'Alumno',
                                    ];
                                }
                                return [];
                            })
                            ->required()
                            ->live()
                            ->afterStateHydrated(function (Select $componente, $record) {
                                if ($record) {
                                    $componente->state($record->getRoleNames()->first());
                                }
                            }),
                        FileUpload::make('avatar_url')
                            ->label('Foto de Perfil')
                            ->image()
                            ->disk('public')
                            ->directory('avatars')
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->columnSpanFull(),
                    ])->columns(2),

                // Perfil: Alumno
                Section::make('Datos de Alumno')
                    ->description('Información académica y de contacto del estudiante.')
                    ->schema([
                        TextInput::make('datosPerfil.dni')
                            ->label('DNI/NIE')
                            ->placeholder('12345678X')
                            ->required(),
                        DatePicker::make('datosPerfil.fecha_nacimiento')
                            ->label('Fecha de Nacimiento')
                            ->placeholder('Selecciona fecha'),
                        TextInput::make('datosPerfil.telefono')
                            ->label('Teléfono')
                            ->placeholder('600123456'),
                        Select::make('datosPerfil.curso_id')
                            ->label('Curso / Grupo')
                            ->placeholder('Selecciona curso')
                            ->relationship('alumno.curso', 'nombre')
                            ->searchable()
                            ->preload(),
                        Select::make('datosPerfil.empresa_id')
                            ->label('Empresa Asignada')
                            ->placeholder('Selecciona empresa')
                            ->relationship('alumno.empresa', 'nombre')
                            ->searchable()
                            ->preload(),
                        Select::make('datosPerfil.tutor_practicas_id')
                            ->label('Tutor de Empresa')
                            ->placeholder('Selecciona tutor')
                            ->options(User::role('tutor_practicas')->pluck('name', 'id'))
                            ->searchable(),
                        TextInput::make('datosPerfil.duracion_practicas')
                            ->label('Duración (horas)')
                            ->placeholder('Ej: 400'),
                        TextInput::make('datosPerfil.horario')
                            ->label('Horario de Prácticas')
                            ->placeholder('Ej: 08:00 - 14:00'),
                        DatePicker::make('datosPerfil.fecha_inicio')
                            ->label('Fecha de Inicio')
                            ->placeholder('Selecciona fecha'),
                        DatePicker::make('datosPerfil.fecha_fin')
                            ->label('Fecha de Finalización')
                            ->placeholder('Selecciona fecha'),
                    ])
                    ->visible(fn (Get $get) => $get('rol') === 'alumno')
                    ->columns(2),

                // Perfil: Tutor Practicas
                Section::make('Datos de Tutor de Empresa')
                    ->description('Información laboral y de contacto del tutor externo.')
                    ->schema([
                        TextInput::make('datosPerfil.dni')
                            ->label('DNI/NIE')
                            ->placeholder('12345678X')
                            ->required(),
                        TextInput::make('datosPerfil.telefono')
                            ->label('Teléfono de Contacto')
                            ->placeholder('600123456'),
                        Select::make('datosPerfil.empresa_id')
                            ->label('Empresa a la que pertenece')
                            ->placeholder('Selecciona empresa')
                            ->relationship('perfilTutorPracticas.empresa', 'nombre')
                            ->required(),
                        TextInput::make('datosPerfil.puesto')
                            ->label('Cargo o Puesto')
                            ->placeholder('Ej: Responsable IT'),
                        TextInput::make('datosPerfil.horario')
                            ->label('Horario Laboral')
                            ->placeholder('Ej: Jornada completa'),
                    ])
                    ->visible(fn (Get $get) => $get('rol') === 'tutor_practicas')
                    ->columns(2),

                // Perfil: Tutor Curso
                Section::make('Datos de Tutor de Centro')
                    ->description('Información docente del tutor del instituto.')
                    ->schema([
                        TextInput::make('datosPerfil.dni')
                            ->label('DNI/NIE')
                            ->placeholder('12345678X')
                            ->required(),
                        TextInput::make('datosPerfil.telefono')
                            ->label('Teléfono')
                            ->placeholder('600123456'),
                        TextInput::make('datosPerfil.especialidad')
                            ->label('Especialidad Docente')
                            ->placeholder('Ej: Informática'),
                    ])
                    ->visible(fn (Get $get) => $get('rol') === 'tutor_curso')
                    ->columns(2),

                // Perfil: Empresa (como perfil de usuario)
                Section::make('Datos de Empresa')
                    ->description('Información fiscal y corporativa.')
                    ->schema([
                        TextInput::make('datosPerfil.cif')
                            ->label('CIF')
                            ->placeholder('B12345678')
                            ->required(),
                        TextInput::make('datosPerfil.direccion')
                            ->label('Dirección Fiscal')
                            ->placeholder('Calle...'),
                        TextInput::make('datosPerfil.telefono')
                            ->label('Teléfono Corporativo')
                            ->placeholder('912345678'),
                        TextInput::make('datosPerfil.persona_contacto')
                            ->label('Persona de Contacto')
                            ->placeholder('Nombre del responsable'),
                        TextInput::make('datosPerfil.sector')
                            ->label('Sector de Actividad')
                            ->placeholder('Ej: Desarrollo de software'),
                    ])
                    ->visible(fn (Get $get) => $get('rol') === 'empresa')
                    ->columns(2),
            ]);
    }

    /**
     * @brief Obtiene la tabla configurada para el recurso Usuario.
     * 
     * @param Table $tabla Objeto de la tabla.
     * @return Table Tabla configurada con columnas dinámicas por rol.
     */
    public static function table(Table $tabla): Table
    {
        return $tabla
            ->columns([
                ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->toggleable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Rol')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'admin' => 'danger',
                        'alumno' => 'success',
                        'tutor_practicas' => 'warning',
                        'tutor_curso' => 'info',
                        'empresa' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        'admin' => 'Administrador',
                        'alumno' => 'Alumno',
                        'tutor_practicas' => 'Tutor Empresa',
                        'tutor_curso' => 'Tutor Centro',
                        'empresa' => 'Empresa',
                        default => $state,
                    }),
                TextColumn::make('perfil_detallado')
                    ->label('ID Perfil')
                    ->getStateUsing(function (User $record) {
                        $rol = $record->getRoleNames()->first();
                        return match ($rol) {
                            'alumno' => "ALU-{$record->reference_id}",
                            'tutor_practicas' => "T EMP-{$record->reference_id}",
                            'tutor_curso' => "T CEN-{$record->reference_id}",
                            'empresa' => "EMP-{$record->reference_id}",
                            default => 'N/A',
                        };
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('informacion_adicional')
                    ->label('Información Adicional')
                    ->getStateUsing(function (User $record) {
                        $rol = $record->getRoleNames()->first();
                        return match ($rol) {
                            'alumno' => $record->alumno?->curso?->nombre ?? 'Sin Curso',
                            'tutor_practicas' => $record->perfilTutorPracticas?->empresa?->nombre ?? 'Sin Empresa',
                            'empresa' => $record->empresa?->sector ?? 'Sin Sector',
                            'tutor_curso' => $record->perfilTutorCurso?->especialidad ?? 'Sin Especialidad',
                            default => '-',
                        };
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->label('Filtrar por Rol')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-m-pencil-square')
                    ->button(),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->button()
                    ->before(function (User $record) {
                        self::borrarPerfilRelacionado($record);
                    })
                    ->successNotification(fn (User $record) => 
                        Notification::make()
                            ->success()
                            ->title('Usuario eliminado')
                            ->body("El usuario {$record->name} ha sido eliminado correctamente.")
                            ->sendToDatabase(auth()->user())
                    ),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->before(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each(fn (User $record) => self::borrarPerfilRelacionado($record));
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Usuarios eliminados')
                                ->body('Los usuarios seleccionados han sido eliminados correctamente.')
                                ->sendToDatabase(auth()->user())
                        ),
                ])->label('Acciones por lote'),
            ]);
    }

    /**
     * @brief Borra el perfil relacionado del usuario antes de eliminar al usuario.
     * 
     * @param User $usuario Instancia del usuario a borrar.
     * @return void
     */
    protected static function borrarPerfilRelacionado(User $usuario): void
    {
        $rol = $usuario->getRoleNames()->first();
        
        match ($rol) {
            'alumno' => $usuario->alumno()?->delete(),
            'tutor_practicas' => $usuario->perfilTutorPracticas()?->delete(),
            'tutor_curso' => $usuario->perfilTutorCurso()?->delete(),
            'empresa' => $usuario->empresa()?->delete(),
            default => null,
        };
    }

    /**
     * @brief Obtiene las páginas definidas para la gestión del recurso.
     * 
     * @return array Mapa de rutas y clases de página.
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
