<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncidenciaResource\Pages;
use App\Filament\Resources\IncidenciaResource\RelationManagers;
use App\Models\Incidencia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles de la Incidencia')
                    ->schema([
                        Forms\Components\Select::make('alumno_id')
                            ->relationship('alumno', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? 'Alumno sin usuario')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('tutor_practicas_id')
                            ->relationship('tutorPracticas', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? 'Tutor sin usuario')
                            ->searchable()
                            ->nullable(),
                        Forms\Components\DatePicker::make('fecha')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('tipo')
                            ->options([
                                'FALTA' => 'Falta',
                                'RETRASO' => 'Retraso',
                                'PROBLEMA_ACTITUD' => 'Problema de Actitud',
                                'OTROS' => 'Otros',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('descripcion')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('estado')
                            ->options([
                                'ABIERTA' => 'Abierta',
                                'EN_PROCESO' => 'En Proceso',
                                'RESUELTA' => 'Resuelta',
                            ])
                            ->default('ABIERTA')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Resolución')
                    ->schema([
                        Forms\Components\DateTimePicker::make('fecha_resolucion'),
                        Forms\Components\Textarea::make('resolucion')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['alumno.user', 'tutorPracticas.user']);
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isTutorCurso()) {
            return $query->whereHas('alumno', fn($q) => $q->where('tutor_curso_id', $user->perfilTutorCurso?->id));
        }

        if ($user->isAlumno()) {
            return $query->whereHas('alumno', fn($q) => $q->where('user_id', $user->id));
        }

        if ($user->isTutorPracticas()) {
            return $query->whereHas('alumno', fn($q) => $q->whereHas('tutorPracticas', fn($sq) => $sq->where('user_id', $user->id)));
        }

        return $query->whereRaw('1 = 0');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->columns([
                Tables\Columns\TextColumn::make('alumno.user.name')
                    ->label('Alumno')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tutorPracticas.user.name')
                    ->label('Tutor Prácticas')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ABIERTA' => 'danger',
                        'EN_PROCESO' => 'warning',
                        'RESUELTA' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Creación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_resolucion')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'ABIERTA' => 'Abierta',
                        'EN_PROCESO' => 'En Proceso',
                        'RESUELTA' => 'Resuelta',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('marcarComoResuelta')
                    ->label('Resolver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->disabled(fn (Incidencia $record): bool => $record->estado === 'RESUELTA')
                    ->form([
                        Forms\Components\Textarea::make('resolucion')
                            ->label('Explicación de la resolución')
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
                            ->sendToDatabase(\Filament\Facades\Filament::auth()->user())
                    )
                    ->modalHeading('Resolver Incidencia')
                    ->modalDescription('Por favor, indica cómo se ha resuelto la incidencia.')
                    ->modalSubmitActionLabel('Confirmar Resolución'),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Incidencia eliminada')
                            ->body("La incidencia ha sido eliminada correctamente.")
                            ->sendToDatabase(\Filament\Facades\Filament::auth()->user())
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Incidencias eliminadas')
                                ->body("Las incidencias seleccionadas han sido eliminadas correctamente.")
                                ->sendToDatabase(\Filament\Facades\Filament::auth()->user())
                        ),
                ]),
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
