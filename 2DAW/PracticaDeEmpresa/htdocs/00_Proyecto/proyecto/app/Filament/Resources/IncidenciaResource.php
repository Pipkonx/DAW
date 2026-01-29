<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncidenciaResource\Pages;
use App\Models\Incidencia;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class IncidenciaResource extends Resource
{
    protected static ?string $model = Incidencia::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'Gestión Académica';

    protected static ?string $modelLabel = 'Incidencia';

    protected static ?string $pluralModelLabel = 'Incidencias';

    /**
     * @brief Obtiene el formulario configurado para el recurso Incidencia.
     * 
     * @param Form $formulario Objeto del formulario.
     * @return Form Formulario configurado con detalles de la incidencia y resolución.
     */
    public static function form(Form $formulario): Form
    {
        return $formulario
            ->schema([
                Section::make('Detalles de la Incidencia')
                    ->description('Información sobre el alumno, tipo de incidencia y descripción.')
                    ->schema([
                        Select::make('alumno_id')
                            ->label('Alumno')
                            ->placeholder('Selecciona un alumno')
                            ->relationship('alumno', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? 'Alumno sin usuario')
                            ->searchable()
                            ->required(),
                        Select::make('tutor_practicas_id')
                            ->label('Tutor de Empresa')
                            ->placeholder('Selecciona un tutor (opcional)')
                            ->relationship('tutorPracticas', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? 'Tutor sin usuario')
                            ->searchable()
                            ->nullable(),
                        DatePicker::make('fecha')
                            ->label('Fecha de la Incidencia')
                            ->placeholder('Selecciona fecha')
                            ->required()
                            ->default(now()),
                        Select::make('tipo')
                            ->label('Tipo de Incidencia')
                            ->placeholder('Selecciona tipo')
                            ->options([
                                'FALTA' => 'Falta de asistencia',
                                'RETRASO' => 'Retraso',
                                'PROBLEMA_ACTITUD' => 'Problema de Actitud',
                                'OTROS' => 'Otros',
                            ])
                            ->required(),
                        Textarea::make('descripcion')
                            ->label('Descripción Detallada')
                            ->placeholder('Describe lo sucedido...')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('estado')
                            ->label('Estado Inicial')
                            ->placeholder('Selecciona estado')
                            ->options([
                                'ABIERTA' => 'Abierta',
                                'EN_PROCESO' => 'En Proceso',
                                'RESUELTA' => 'Resuelta',
                            ])
                            ->default('ABIERTA')
                            ->required(),
                    ])->columns(2),

                Section::make('Resolución')
                    ->description('Datos sobre cómo y cuándo se resolvió la incidencia.')
                    ->schema([
                        DateTimePicker::make('fecha_resolucion')
                            ->label('Fecha de Resolución')
                            ->placeholder('Selecciona fecha y hora'),
                        Textarea::make('resolucion')
                            ->label('Explicación de la Resolución')
                            ->placeholder('Detalla cómo se ha resuelto...')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /**
     * @brief Obtiene la consulta base optimizada para el recurso Incidencia.
     * 
     * @return Builder Consulta configurada con carga ansiosa y filtros por rol.
     */
    public static function getEloquentQuery(): Builder
    {
        $consulta = parent::getEloquentQuery()->with(['alumno.user', 'tutorPracticas.user']);
        $usuarioActual = auth()->user();

        if ($usuarioActual->isAdmin()) {
            return $consulta;
        }

        if ($usuarioActual->isTutorCurso()) {
            return $consulta->whereHas('alumno', fn($subconsulta) => $subconsulta->where('tutor_curso_id', $usuarioActual->perfilTutorCurso?->id));
        }

        if ($usuarioActual->isAlumno()) {
            return $consulta->whereHas('alumno', fn($subconsulta) => $subconsulta->where('user_id', $usuarioActual->id));
        }

        if ($usuarioActual->isTutorPracticas()) {
            return $consulta->whereHas('alumno', fn($subconsulta) => $subconsulta->whereHas('tutorPracticas', fn($sq) => $sq->where('user_id', $usuarioActual->id)));
        }

        return $consulta->whereRaw('1 = 0');
    }

    /**
     * @brief Obtiene la tabla configurada para el recurso Incidencia.
     * 
     * @param Table $tabla Objeto de la tabla.
     * @return Table Tabla configurada con columnas de estado, tipo y acciones de resolución.
     */
    public static function table(Table $tabla): Table
    {
        return $tabla
            ->deferLoading()
            ->columns([
                TextColumn::make('alumno.user.name')
                    ->label('Alumno')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tutorPracticas.user.name')
                    ->label('Tutor Empresa')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('tipo')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state): string => match ($state) {
                        'FALTA' => 'Falta',
                        'RETRASO' => 'Retraso',
                        'PROBLEMA_ACTITUD' => 'Actitud',
                        'OTROS' => 'Otros',
                        default => $state,
                    })
                    ->searchable()
                    ->badge(),
                TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'ABIERTA' => 'danger',
                        'EN_PROCESO' => 'warning',
                        'RESUELTA' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->label('Creada el')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('fecha_resolucion')
                    ->label('Resuelta el')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->label('Filtrar por Estado')
                    ->options([
                        'ABIERTA' => 'Abierta',
                        'EN_PROCESO' => 'En Proceso',
                        'RESUELTA' => 'Resuelta',
                    ]),
                SelectFilter::make('tipo')
                    ->label('Filtrar por Tipo')
                    ->options([
                        'FALTA' => 'Falta',
                        'RETRASO' => 'Retraso',
                        'PROBLEMA_ACTITUD' => 'Actitud',
                        'OTROS' => 'Otros',
                    ]),
            ])
            ->actions([
                Action::make('marcarComoResuelta')
                    ->label('Resolver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->disabled(fn (Incidencia $record): bool => $record->estado === 'RESUELTA')
                    ->form([
                        Textarea::make('resolucion')
                            ->label('Explicación de la resolución')
                            ->placeholder('Describe cómo se ha solucionado...')
                            ->required(),
                    ])
                    ->action(function (Incidencia $record, array $data): void {
                        $record->update([
                            'estado' => 'RESUELTA',
                            'fecha_resolucion' => now(),
                            'resolucion' => $data['resolucion'],
                        ]);
                    })
                    ->successNotification(fn (Incidencia $record) => 
                        Notification::make()
                            ->success()
                            ->title('Incidencia resuelta')
                            ->body("La incidencia del alumno " . ($record->alumno?->user?->name ?? 'desconocido') . " ha sido marcada como resuelta.")
                            ->sendToDatabase(auth()->user())
                    )
                    ->modalHeading('Resolver Incidencia')
                    ->modalDescription('Por favor, indica cómo se ha resuelto la incidencia.')
                    ->modalSubmitActionLabel('Confirmar Resolución'),
                ViewAction::make()
                    ->label('Ver'),
                EditAction::make()
                    ->label('Editar'),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Incidencia eliminada')
                            ->body("La incidencia ha sido eliminada correctamente.")
                            ->sendToDatabase(auth()->user())
                    ),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionadas')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Incidencias eliminadas')
                                ->body("Las incidencias seleccionadas han sido eliminadas correctamente.")
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
            'index' => Pages\ListIncidencias::route('/'),
            'create' => Pages\CreateIncidencia::route('/create'),
            'view' => Pages\ViewIncidencia::route('/{record}'),
            'edit' => Pages\EditIncidencia::route('/{record}/edit'),
        ];
    }
}
