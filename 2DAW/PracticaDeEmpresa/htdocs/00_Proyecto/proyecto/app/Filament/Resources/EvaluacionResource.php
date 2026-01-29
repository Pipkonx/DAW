<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluacionResource\Pages;
use App\Models\Evaluacion;
use App\Models\Alumno;
use App\Models\TutorPracticas;
use App\Models\CapacidadEvaluacion;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;

class EvaluacionResource extends Resource
{
    protected static ?string $model = Evaluacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Gestión Académica';

    protected static ?string $modelLabel = 'Evaluación';

    protected static ?string $pluralModelLabel = 'Evaluaciones';

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isTutorCurso();
    }

    /**
     * @brief Obtiene el formulario configurado para el recurso Evaluación.
     * 
     * @param Form $formulario Objeto del formulario.
     * @return Form Formulario configurado con campos de alumno, tutor y criterios.
     */
    public static function form(Form $formulario): Form
    {
        return $formulario
            ->schema([
                Section::make('Información General')
                    ->description('Datos del alumno y tutor responsables de la evaluación.')
                    ->schema([
                        Select::make('alumno_id')
                            ->label('Alumno a evaluar')
                            ->placeholder('Selecciona un alumno')
                            ->relationship('alumno', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? 'Alumno sin usuario')
                            ->options(function () {
                                $usuarioActual = auth()->user();
                                if ($usuarioActual->isAdmin() || $usuarioActual->isTutorCurso()) {
                                    return Alumno::with('user')->get()->pluck('user.name', 'id');
                                }
                                if ($usuarioActual->isTutorPracticas()) {
                                    return Alumno::with('user')->whereHas('tutorPracticas', function ($subconsulta) use ($usuarioActual) {
                                        $subconsulta->where('user_id', $usuarioActual->id);
                                    })
                                        ->get()
                                        ->pluck('user.name', 'id');
                                }

                                return [];
                            })
                            ->searchable()
                            ->required(),
                        Select::make('tutor_practicas_id')
                            ->label('Tutor de Empresa')
                            ->placeholder('Selecciona tutor')
                            ->relationship('tutorPracticas', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? 'Tutor sin usuario')
                            ->default(function () {
                                $usuarioActual = auth()->user();
                                if ($usuarioActual->isTutorPracticas()) {
                                    return TutorPracticas::where('user_id', $usuarioActual->id)->first()?->id;
                                }

                                return null;
                            })
                            ->required(),
                        TextInput::make('nota_final')
                            ->label('Calificación Final')
                            ->placeholder('Cálculo automático')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(true)
                            ->helperText('Esta nota se calcula automáticamente en base a los criterios.'),
                    ])->columns(2),

                Section::make('Criterios de Evaluación')
                    ->description('Calificaciones detalladas por capacidad o competencia.')
                    ->schema([
                        Repeater::make('detalles')
                            ->label('Capacidades Evaluadas')
                            ->relationship()
                            ->schema([
                                Select::make('capacidad_id')
                                    ->label('Capacidad / Competencia')
                                    ->placeholder('Selecciona capacidad')
                                    ->relationship('capacidad', 'nombre')
                                    ->required()
                                    ->columnSpan(2),
                                TextInput::make('nota')
                                    ->label('Calificación (0-10)')
                                    ->placeholder('Ej: 8.5')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(10)
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->itemLabel(function (array $state): ?string {
                                static $capacidades = null;
                                if ($capacidades === null) {
                                    $capacidades = CapacidadEvaluacion::pluck('nombre', 'id');
                                }

                                return $capacidades[$state['capacidad_id']] ?? 'Nuevo Criterio';
                            })
                            ->defaultItems(1)
                            ->addActionLabel('Añadir Criterio'),
                    ]),

                Section::make('Observaciones')
                    ->description('Comentarios adicionales sobre el desempeño del alumno.')
                    ->schema([
                        Textarea::make('observaciones')
                            ->label('Comentarios del Tutor')
                            ->placeholder('Escribe aquí cualquier observación relevante...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * @brief Obtiene la consulta base optimizada para el recurso Evaluación.
     * 
     * @return Builder Consulta configurada con carga ansiosa de relaciones.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['alumno.user', 'tutorPracticas.user']);
    }

    /**
     * @brief Obtiene la tabla configurada para el recurso Evaluación.
     * 
     * @param Table $tabla Objeto de la tabla.
     * @return Table Tabla configurada con columnas de alumno, tutor y nota final.
     */
    public static function table(Table $tabla): Table
    {
        return $tabla
            ->deferLoading()
            ->columns([
                TextColumn::make('alumno.user.name')
                    ->label('Estudiante')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tutorPracticas.user.name')
                    ->label('Tutor Empresa')
                    ->sortable(),
                TextColumn::make('nota_final')
                    ->label('Nota Final')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state >= 5 ? 'success' : 'danger'),
                TextColumn::make('created_at')
                    ->label('Evaluado el')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('alumno')
                    ->label('Filtrar por Estudiante')
                    ->relationship('alumno', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? 'Alumno sin usuario'),
            ])
            ->actions([
                EditAction::make()
                    ->label('Editar'),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Evaluación eliminada')
                            ->body('La evaluación ha sido eliminada correctamente.')
                            ->sendToDatabase(Filament::auth()->user())
                    ),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionadas')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Evaluaciones eliminadas')
                                ->body('Las evaluaciones seleccionadas han sido eliminadas correctamente.')
                                ->sendToDatabase(Filament::auth()->user())
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
            'index' => Pages\ListEvaluacions::route('/'),
            'create' => Pages\CreateEvaluacion::route('/create'),
            'edit' => Pages\EditEvaluacion::route('/{record}/edit'),
        ];
    }
}
