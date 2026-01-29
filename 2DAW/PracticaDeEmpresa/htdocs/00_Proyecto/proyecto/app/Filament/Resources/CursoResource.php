<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CursoResource\Pages;
use App\Models\Curso;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CursoResource extends Resource
{
    protected static ?string $model = Curso::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Gestión Académica';

    protected static ?string $modelLabel = 'Curso';

    protected static ?string $pluralModelLabel = 'Cursos';

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || 
               auth()->user()->isTutorCurso() || 
               auth()->user()->hasPermissionTo('gestionar_cursos');
    }

    /**
     * @brief Obtiene la consulta base optimizada para el recurso Curso.
     * 
     * @return Builder Consulta configurada con carga ansiosa y filtros por rol.
     */
    public static function getEloquentQuery(): Builder
    {
        $consulta = parent::getEloquentQuery()->with(['tutorCurso']);
        $usuarioActual = auth()->user();

        if ($usuarioActual->isAdmin()) {
            return $consulta;
        }

        if ($usuarioActual->isTutorCurso()) {
            return $consulta->where('tutor_curso_id', $usuarioActual->perfilTutorCurso?->id);
        }

        return $consulta->whereRaw('1 = 0');
    }

    /**
     * @brief Obtiene el formulario configurado para el recurso Curso.
     * 
     * @param Form $formulario Objeto del formulario.
     * @return Form Formulario configurado con validaciones de fechas y relaciones.
     */
    public static function form(Form $formulario): Form
    {
        return $formulario
            ->schema([
                Section::make('Información del Curso')
                    ->description('Detalles generales del programa académico.')
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre del Curso')
                            ->placeholder('Ej: 2º DAW - Desarrollo de Aplicaciones Web')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('descripcion')
                            ->label('Descripción')
                            ->placeholder('Breve descripción del curso...')
                            ->rows(3)
                            ->maxLength(65535),
                        TextInput::make('duracion')
                            ->label('Duración (Horas)')
                            ->placeholder('Ej: 400')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        Select::make('tutor_curso_id')
                            ->label('Tutor del Curso')
                            ->placeholder('Selecciona un tutor')
                            ->relationship('tutorCurso', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Section::make('Periodo Académico')
                    ->description('Fechas de inicio, fin y estado de activación.')
                    ->schema([
                        DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio')
                            ->placeholder('Selecciona fecha')
                            ->required()
                            ->live(),
                        DatePicker::make('fecha_fin')
                            ->label('Fecha de Finalización')
                            ->placeholder('Selecciona fecha')
                            ->required()
                            ->afterOrEqual('fecha_inicio')
                            ->validationMessages([
                                'after_or_equal' => 'La fecha de fin no puede ser anterior a la fecha de inicio.',
                            ]),
                        Toggle::make('activo')
                            ->label('Curso Activo')
                            ->helperText('Indica si el curso está actualmente en curso.')
                            ->default(true)
                            ->required(),
                    ])->columns(2),
            ]);
    }

    /**
     * @brief Obtiene la tabla configurada para el recurso Curso.
     * 
     * @param Table $tabla Objeto de la tabla.
     * @return Table Tabla configurada con columnas de relación y acciones.
     */
    public static function table(Table $tabla): Table
    {
        return $tabla
            ->deferLoading()
            ->columns([
                TextColumn::make('nombre')
                    ->label('Curso')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tutorCurso.nombre')
                    ->label('Tutor Asignado')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('duracion')
                    ->label('Horas')
                    ->suffix(' h')
                    ->sortable(),
                IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('activo')
                    ->label('Solo activos'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Editar'),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->successNotification(fn (Curso $record) => 
                        Notification::make()
                            ->success()
                            ->title('Curso eliminado')
                            ->body("El curso {$record->nombre} ha sido eliminado correctamente.")
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
                                ->title('Cursos eliminados')
                                ->body("Los cursos seleccionados han sido eliminados correctamente.")
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
            'index' => Pages\ListCursos::route('/'),
            'create' => Pages\CreateCurso::route('/create'),
            'edit' => Pages\EditCurso::route('/{record}/edit'),
        ];
    }
}
