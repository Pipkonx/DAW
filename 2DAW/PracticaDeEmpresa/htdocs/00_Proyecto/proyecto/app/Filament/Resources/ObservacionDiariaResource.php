<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ObservacionDiariaResource\Pages;
use App\Filament\Resources\ObservacionDiariaResource\RelationManagers;
use App\Models\ObservacionDiaria;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('alumno_id')
                    ->relationship('alumno', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? 'Alumno sin usuario')
                    ->searchable(['user.name'])
                    ->default(fn () => auth()->user()->isAlumno() ? auth()->user()->alumno?->id : null)
                    ->hidden(fn () => auth()->user()->isAlumno())
                    ->required(),
                Forms\Components\DatePicker::make('fecha')
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('horasRealizadas')
                    ->label('Horas Realizadas')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('actividades')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('explicaciones')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('observacionesAlumno')
                    ->label('Observaciones Alumno')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('observacionesTutor')
                    ->label('Observaciones Tutor')
                    ->columnSpanFull()
                    ->disabled(fn () => auth()->user()->isAlumno() || auth()->user()->isTutorCurso())
                    ->dehydrated(fn () => !auth()->user()->isAlumno() && !auth()->user()->isTutorCurso())
                    ->visible(fn ($record) => !auth()->user()->isAlumno() || ($record && filled($record->observacionesTutor))),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['alumno.user']);
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
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('horasRealizadas')
                    ->label('Horas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('actividades')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('alumno')
                    ->relationship('alumno', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? 'Alumno sin usuario'),
                Tables\Filters\Filter::make('fecha')
                    ->form([
                        Forms\Components\DatePicker::make('desde'),
                        Forms\Components\DatePicker::make('hasta'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['desde'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha', '>=', $date),
                            )
                            ->when(
                                $data['hasta'],
                                fn (Builder $query, $date): Builder => $query->whereDate('fecha', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Observación eliminada')
                            ->body("La observación diaria ha sido eliminada correctamente.")
                            ->sendToDatabase(\Filament\Facades\Filament::auth()->user())
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Observaciones eliminadas')
                                ->body("Las observaciones seleccionadas han sido eliminadas correctamente.")
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
            'index' => Pages\ListObservacionDiarias::route('/'),
            'create' => Pages\CreateObservacionDiaria::route('/create'),
            'edit' => Pages\EditObservacionDiaria::route('/{record}/edit'),
        ];
    }
}
