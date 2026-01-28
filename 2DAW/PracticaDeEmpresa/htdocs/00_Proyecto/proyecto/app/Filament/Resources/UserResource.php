<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isTutorPracticas();
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isTutorPracticas();
    }

    public static function canCreate(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isTutorPracticas();
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->isAdmin() || (auth()->user()->isTutorPracticas() && $record->hasRole('alumno'));
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->isAdmin() || (auth()->user()->isTutorPracticas() && $record->hasRole('alumno'));
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isTutorPracticas()) {
            return $query->role('alumno');
        }

        return $query->where('id', $user->id);
    }

    /**
     * @brief Define el formulario dinámico basado en el rol seleccionado.
     * 
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de Usuario')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->revealable(),
                        Forms\Components\Select::make('rol')
                            ->label('Rol del Usuario')
                            ->options(function () {
                                if (auth()->user()->isAdmin()) {
                                    return [
                                        'admin' => 'Administrador',
                                        'alumno' => 'Alumno',
                                        'tutor_practicas' => 'Tutor de Prácticas',
                                        'tutor_curso' => 'Tutor de Curso',
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
                            ->afterStateHydrated(function (Forms\Components\Select $component, $record) {
                                if ($record) {
                                    $component->state($record->getRoleNames()->first());
                                }
                            }),
                        Forms\Components\FileUpload::make('avatar_url')
                            ->label('Avatar')
                            ->image()
                            ->disk('public')
                            ->directory('avatars')
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->columnSpanFull(),
                    ])->columns(2),

                // Perfil: Alumno
                Forms\Components\Section::make('Datos de Alumno')
                    ->schema([
                        Forms\Components\TextInput::make('datosPerfil.dni')->label('DNI')->required(),
                        Forms\Components\DatePicker::make('datosPerfil.fecha_nacimiento')->label('Fecha Nacimiento'),
                        Forms\Components\TextInput::make('datosPerfil.telefono')->label('Teléfono'),
                        Forms\Components\Select::make('datosPerfil.curso_id')
                            ->label('Curso')
                            ->relationship('alumno.curso', 'nombre')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('datosPerfil.empresa_id')
                            ->label('Empresa')
                            ->relationship('alumno.empresa', 'nombre')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('datosPerfil.tutor_practicas_id')
                            ->label('Tutor de Empresa')
                            ->options(User::role('tutor_practicas')->pluck('name', 'id'))
                            ->searchable(),
                        Forms\Components\TextInput::make('datosPerfil.duracion_practicas')->label('Duración Prácticas'),
                        Forms\Components\TextInput::make('datosPerfil.horario')->label('Horario'),
                        Forms\Components\DatePicker::make('datosPerfil.fecha_inicio')->label('Fecha Inicio'),
                        Forms\Components\DatePicker::make('datosPerfil.fecha_fin')->label('Fecha Fin'),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('rol') === 'alumno')
                    ->columns(2),

                // Perfil: Tutor Practicas
                Forms\Components\Section::make('Datos de Tutor de Prácticas')
                    ->schema([
                        Forms\Components\TextInput::make('datosPerfil.dni')->label('DNI')->required(),
                        Forms\Components\TextInput::make('datosPerfil.telefono')->label('Teléfono'),
                        Forms\Components\Select::make('datosPerfil.empresa_id')
                            ->label('Empresa')
                            ->relationship('perfilTutorPracticas.empresa', 'nombre')
                            ->required(),
                        Forms\Components\TextInput::make('datosPerfil.puesto')->label('Cargo'),
                        Forms\Components\TextInput::make('datosPerfil.horario')->label('Horario'),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('rol') === 'tutor_practicas')
                    ->columns(2),

                // Perfil: Tutor Curso
                Forms\Components\Section::make('Datos de Tutor de Curso')
                    ->schema([
                        Forms\Components\TextInput::make('datosPerfil.dni')->label('DNI')->required(),
                        Forms\Components\TextInput::make('datosPerfil.telefono')->label('Teléfono'),
                        Forms\Components\TextInput::make('datosPerfil.especialidad')->label('Especialidad'),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('rol') === 'tutor_curso')
                    ->columns(2),

                // Perfil: Empresa (como perfil de usuario)
                Forms\Components\Section::make('Datos de Empresa')
                    ->schema([
                        Forms\Components\TextInput::make('datosPerfil.cif')->label('CIF')->required(),
                        Forms\Components\TextInput::make('datosPerfil.direccion')->label('Dirección'),
                        Forms\Components\TextInput::make('datosPerfil.telefono')->label('Teléfono'),
                        Forms\Components\TextInput::make('datosPerfil.persona_contacto')->label('Persona de Contacto'),
                        Forms\Components\TextInput::make('datosPerfil.sector')->label('Sector'),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('rol') === 'empresa')
                    ->columns(2),
            ]);
    }

    /**
     * @brief Configura la tabla de usuarios con columnas personalizadas.
     * 
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'alumno' => 'success',
                        'tutor_practicas' => 'warning',
                        'tutor_curso' => 'info',
                        'empresa' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('perfil_detallado')
                    ->label('Perfil Detallado')
                    ->getStateUsing(function (User $record) {
                        $rol = $record->getRoleNames()->first();
                        return match ($rol) {
                            'alumno' => "Alumno ID: {$record->reference_id}",
                            'tutor_practicas' => "Tutor Prác. ID: {$record->reference_id}",
                            'tutor_curso' => "Tutor Curso ID: {$record->reference_id}",
                            'empresa' => "Empresa ID: {$record->reference_id}",
                            default => 'N/A',
                        };
                    }),
                Tables\Columns\TextColumn::make('informacion_adicional')
                    ->label('Info. Adicional')
                    ->getStateUsing(function (User $record) {
                        $rol = $record->getRoleNames()->first();
                        return match ($rol) {
                            'alumno' => $record->alumno?->curso?->nombre ?? 'Sin Curso',
                            'tutor_practicas' => $record->perfilTutorPracticas?->empresa?->nombre ?? 'Sin Empresa',
                            'empresa' => $record->empresa?->sector ?? 'Sin Sector',
                            'tutor_curso' => $record->perfilTutorCurso?->especialidad ?? 'Sin Especialidad',
                            default => '-',
                        };
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Filtrar por Rol')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (User $record) {
                        self::borrarPerfilRelacionado($record);
                    })
                    ->successNotification(fn (User $record) => 
                        Notification::make()
                            ->success()
                            ->title('Usuario eliminado')
                            ->body("El usuario {$record->name} ha sido eliminado correctamente.")
                            ->sendToDatabase(\Filament\Facades\Filament::auth()->user())
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each(fn (User $record) => self::borrarPerfilRelacionado($record));
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Usuarios eliminados')
                                ->body('Los usuarios seleccionados han sido eliminados correctamente.')
                                ->sendToDatabase(\Filament\Facades\Filament::auth()->user())
                        ),
                ]),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
