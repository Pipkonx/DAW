<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmpresaResource\Pages;
use App\Models\Empresa;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmpresaResource extends Resource
{
    protected static ?string $model = Empresa::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Gestión Académica';

    protected static ?string $modelLabel = 'Empresa';

    protected static ?string $pluralModelLabel = 'Empresas';

    /**
     * @brief Determina si el usuario puede ver el recurso de Empresas.
     * 
     * @return bool Verdadero si es admin o tutor de curso.
     */
    public static function puedeVerTodo(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isTutorCurso();
    }

    public static function canViewAny(): bool
    {
        return static::puedeVerTodo();
    }

    /**
     * @brief Configura el formulario para el recurso Empresa.
     * 
     * @param Form $formulario Instancia del formulario.
     * @return Form Formulario configurado con secciones y validaciones.
     */
    public static function form(Form $formulario): Form
    {
        return $formulario
            ->schema([
                Section::make('Información de la Empresa')
                    ->description('Datos generales y de identificación fiscal.')
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre de la Empresa')
                            ->placeholder('Ej: Innova Tech S.L.')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('cif')
                            ->label('CIF')
                            ->placeholder('Ej: B12345678')
                            ->required()
                            ->regex('/^[ABCDEFGHJNPQRSUVW][0-9]{7}[0-9A-J]$/')
                            ->validationMessages([
                                'regex' => 'El formato del CIF no es válido.',
                            ]),
                        TextInput::make('sector')
                            ->label('Sector de Actividad')
                            ->placeholder('Ej: Informática, Hostelería...')
                            ->maxLength(255),
                        Toggle::make('activa')
                            ->label('Empresa Activa')
                            ->default(true),
                    ])->columns(2),

                Section::make('Contacto y Ubicación')
                    ->description('Detalles de localización y medios de contacto.')
                    ->schema([
                        TextInput::make('direccion')
                            ->label('Dirección')
                            ->placeholder('Calle, número, piso...')
                            ->maxLength(255),
                        TextInput::make('localidad')
                            ->label('Localidad')
                            ->placeholder('Nombre de la ciudad o pueblo')
                            ->maxLength(255),
                        TextInput::make('provincia')
                            ->label('Provincia')
                            ->placeholder('Ej: Madrid, Barcelona...')
                            ->maxLength(255),
                        TextInput::make('codigo_postal')
                            ->label('Código Postal')
                            ->placeholder('Ej: 28001')
                            ->maxLength(10),
                        TextInput::make('telefono')
                            ->label('Teléfono')
                            ->placeholder('Ej: 912345678')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->placeholder('Ej: contacto@empresa.com')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('web')
                            ->label('Sitio Web')
                            ->placeholder('Ej: https://www.empresa.com')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('persona_contacto')
                            ->label('Persona de Contacto')
                            ->placeholder('Nombre del responsable')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    /**
     * @brief Configura la tabla para el recurso Empresa.
     * 
     * @param Table $tabla Instancia de la tabla.
     * @return Table Tabla configurada con columnas, filtros y acciones en castellano.
     */
    public static function table(Table $tabla): Table
    {
        return $tabla
            ->deferLoading()
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cif')
                    ->label('CIF')
                    ->searchable(),
                TextColumn::make('sector')
                    ->label('Sector')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('persona_contacto')
                    ->label('Contacto')
                    ->searchable(),
                TextColumn::make('localidad')
                    ->label('Localidad')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('activa')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                    ->sortable(),
                TextColumn::make('fecha_creacion')
                    ->label('Fecha Alta')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('sector')
                    ->label('Filtrar por Sector')
                    ->options(fn () => Empresa::pluck('sector', 'sector')->filter()->toArray()),
                TernaryFilter::make('activa')
                    ->label('Estado Activa')
                    ->placeholder('Todos los estados')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Editar'),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->successNotification(fn (Empresa $record) => 
                        Notification::make()
                            ->success()
                            ->title('Empresa eliminada')
                            ->body("La empresa {$record->nombre} ha sido eliminada correctamente.")
                            ->sendToDatabase(auth()->user())
                    ),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Empresas eliminadas')
                                ->body("Las empresas seleccionadas han sido eliminadas correctamente.")
                                ->sendToDatabase(auth()->user())
                        ),
                ])->label('Acciones por lote'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmpresas::route('/'),
            'create' => Pages\CreateEmpresa::route('/create'),
            'edit' => Pages\EditEmpresa::route('/{record}/edit'),
        ];
    }

    /**
     * @brief Obtiene la consulta base optimizada para el recurso Empresa.
     * 
     * @return Builder Consulta filtrada por los permisos del usuario actual.
     */
    public static function getEloquentQuery(): Builder
    {
        $consulta = parent::getEloquentQuery();
        $usuarioActual = auth()->user();

        if ($usuarioActual->isAdmin() || $usuarioActual->isTutorCurso()) {
            return $consulta;
        }

        if ($usuarioActual->isTutorPracticas()) {
            return $consulta->whereHas('tutoresPracticas', fn($q) => $q->where('user_id', $usuarioActual->id));
        }

        return $consulta->whereRaw('1 = 0');
    }
}
