<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObservacionDiariaResource\Pages;
use App\Models\ObservacionDiaria;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ObservacionDiariaResource extends Resource
{
    protected static ?string $model = ObservacionDiaria::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationGroup = 'Gestión Académica';

    protected static ?string $modelLabel = 'Observación Diaria';

    protected static ?string $pluralModelLabel = 'Observaciones Diarias';

    public static function canCreate(): bool
    {
        return !auth()->user()->isTutorCurso() && !auth()->user()->isTutorPracticas();
    }

    /**
     * @brief Obtiene el formulario configurado para el recurso de Observaciones Diarias.
     * 
     * @param Form $formulario Objeto del formulario.
     * @return Form Formulario configurado con campos de alumno, fecha, horas y textos.
     */
    public static function form(Form $formulario): Form
    {
        return $formulario
            ->schema([
                Select::make('alumno_id')
                    ->label('Alumno')
                    ->placeholder('Selecciona un alumno')
                    ->relationship('alumno', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? 'Alumno sin usuario')
                    ->searchable(['user.name'])
                    ->default(fn () => auth()->user()->isAlumno() ? auth()->user()->alumno?->id : null)
                    ->hidden(fn () => auth()->user()->isAlumno())
                    ->required(),
                DatePicker::make('fecha')
                    ->label('Fecha de la Observación')
                    ->placeholder('Selecciona una fecha')
                    ->default(now())
                    ->required(),
                TextInput::make('horasRealizadas')
                    ->label('Horas Realizadas')
                    ->placeholder('Ej: 8')
                    ->required()
                    ->numeric(),
                Textarea::make('actividades')
                    ->label('Actividades Realizadas')
                    ->placeholder('Describe las tareas completadas hoy...')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('explicaciones')
                    ->label('Explicaciones Adicionales')
                    ->placeholder('Cualquier detalle relevante...')
                    ->columnSpanFull(),
                Textarea::make('observacionesAlumno')
                    ->label('Observaciones del Alumno')
                    ->placeholder('Comentarios del alumno sobre la jornada...')
                    ->columnSpanFull(),
                Textarea::make('observacionesTutor')
                    ->label('Observaciones del Tutor')
                    ->placeholder('Comentarios del tutor de empresa o centro...')
                    ->columnSpanFull()
                    ->disabled(fn () => auth()->user()->isAlumno() || auth()->user()->isTutorCurso())
                    ->dehydrated(fn () => !auth()->user()->isAlumno() && !auth()->user()->isTutorCurso())
                    ->visible(fn ($record) => !auth()->user()->isAlumno() || ($record && filled($record->observacionesTutor))),
            ]);
    }

    /**
     * @brief Obtiene la consulta base optimizada para el recurso de Observaciones Diarias.
     * 
     * @return Builder Consulta filtrada por el rol del usuario actual.
     */
    public static function getEloquentQuery(): Builder
    {
        $consulta = parent::getEloquentQuery()->with(['alumno.user']);
        $usuarioActual = auth()->user();

        if ($usuarioActual->isAdmin()) {
            return $consulta;
        }

        if ($usuarioActual->isTutorCurso()) {
            return $consulta->whereHas('alumno', fn($q) => $q->where('tutor_curso_id', $usuarioActual->perfilTutorCurso?->id));
        }

        if ($usuarioActual->isAlumno()) {
            return $consulta->whereHas('alumno', fn($q) => $q->where('user_id', $usuarioActual->id));
        }

        if ($usuarioActual->isTutorPracticas()) {
            return $consulta->whereHas('alumno', fn($q) => $q->whereHas('tutorPracticas', fn($sq) => $sq->where('user_id', $usuarioActual->id)));
        }

        return $consulta->whereRaw('1 = 0');
    }

    /**
     * @brief Obtiene la tabla configurada para el recurso de Observaciones Diarias.
     * 
     * @param Table $tabla Objeto de la tabla.
     * @return Table Tabla configurada con columnas, filtros y acciones.
     */
    public static function table(Table $tabla): Table
    {
        return $tabla
            ->deferLoading()
            ->columns([
                TextColumn::make('alumno.user.name')
                    ->label('Alumno')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
                TextColumn::make('horasRealizadas')
                    ->label('Horas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('actividades')
                    ->label('Actividades')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Creado el')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('alumno')
                    ->label('Filtrar por Alumno')
                    ->relationship('alumno', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? 'Alumno sin usuario'),
                Filter::make('fecha')
                    ->label('Filtrar por Fecha')
                    ->form([
                        DatePicker::make('desde')
                            ->label('Desde'),
                        DatePicker::make('hasta')
                            ->label('Hasta'),
                    ])
                    ->query(function (Builder $consulta, array $datos): Builder {
                        return $consulta
                            ->when(
                                $datos['desde'],
                                fn (Builder $q, $fecha): Builder => $q->whereDate('fecha', '>=', $fecha),
                            )
                            ->when(
                                $datos['hasta'],
                                fn (Builder $q, $fecha): Builder => $q->whereDate('fecha', '<=', $fecha),
                            );
                    })
            ])
            ->actions([
                EditAction::make()
                    ->label('Editar'),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Observación eliminada')
                            ->body("La observación diaria ha sido eliminada correctamente.")
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
                                ->title('Observaciones eliminadas')
                                ->body("Las observaciones seleccionadas han sido eliminadas correctamente.")
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
            'index' => Pages\ListObservacionDiarias::route('/'),
            'create' => Pages\CreateObservacionDiaria::route('/create'),
            'edit' => Pages\EditObservacionDiaria::route('/{record}/edit'),
        ];
    }
}
