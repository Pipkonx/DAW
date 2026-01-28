<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluacionResource\Pages;
use App\Filament\Resources\EvaluacionResource\RelationManagers;
use App\Models\Evaluacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EvaluacionResource extends Resource
{
    protected static ?string $model = Evaluacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Gestión Académica';

    protected static ?string $modelLabel = 'Evaluación';

    protected static ?string $pluralModelLabel = 'Evaluaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->schema([
                        Forms\Components\Select::make('alumno_id')
                            ->relationship('alumno', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? 'Alumno sin usuario')
                            ->options(function () {
                                $user = auth()->user();
                                if ($user->isAdmin() || $user->isTutorCurso()) {
                                    return \App\Models\Alumno::all()->pluck('user.name', 'id');
                                }
                                if ($user->isTutorPracticas()) {
                                    return \App\Models\Alumno::whereHas('tutorPracticas', function ($q) use ($user) {
                                        $q->where('user_id', $user->id);
                                    })
                                        ->get()
                                        ->pluck('user.name', 'id');
                                }
                                return [];
                            })
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('tutor_practicas_id')
                            ->relationship('tutorPracticas', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? 'Tutor sin usuario')
                            ->default(function () {
                                $user = auth()->user();
                                if ($user->isTutorPracticas()) {
                                    return \App\Models\TutorPracticas::where('user_id', $user->id)->first()?->id;
                                }
                                return null;
                            })
                            ->required(),
                        Forms\Components\TextInput::make('nota_final')
                            ->numeric()
                            ->disabled()
                            ->dehydrated(true)
                            ->label('Nota Final (Autocalculada)'),
                    ])->columns(2),

                Forms\Components\Section::make('Criterios de Evaluación')
                    ->schema([
                        Forms\Components\Repeater::make('detalles')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('capacidad_id')
                                    ->relationship('capacidad', 'nombre')
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('nota')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(10)
                                    ->required()
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => \App\Models\CapacidadEvaluacion::find($state['capacidad_id'])?->nombre ?? 'Nuevo Criterio')
                            ->defaultItems(1),
                    ]),

                Forms\Components\Section::make('Observaciones')
                    ->schema([
                        Forms\Components\Textarea::make('observaciones')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['alumno.user', 'tutorPracticas.user']);
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
                Tables\Columns\TextColumn::make('tutorPracticas.user.name')
                    ->label('Tutor')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nota_final')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state >= 5 ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('alumno')
                    ->relationship('alumno', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name ?? 'Alumno sin usuario'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Evaluación eliminada')
                            ->body("La evaluación ha sido eliminada correctamente.")
                            ->sendToDatabase(\Filament\Facades\Filament::auth()->user())
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Evaluaciones eliminadas')
                                ->body("Las evaluaciones seleccionadas han sido eliminadas correctamente.")
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
            'index' => Pages\ListEvaluacions::route('/'),
            'create' => Pages\CreateEvaluacion::route('/create'),
            'edit' => Pages\EditEvaluacion::route('/{record}/edit'),
        ];
    }
}
