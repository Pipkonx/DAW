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

class IncidenciaResource extends Resource
{
    protected static ?string $model = Incidencia::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'Gestión Académica';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detalles de la Incidencia')
                    ->schema([
                        Forms\Components\Select::make('alumno_id')
                            ->relationship('alumno', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name)
                            ->options(function () {
                                $user = auth()->user();
                                if ($user->isAdmin() || $user->isTutorCurso()) {
                                    return \App\Models\Alumno::all()->pluck('user.name', 'id');
                                }
                                if ($user->isTutorEmpresa()) {
                                    return \App\Models\Alumno::where('empresa_id', $user->empresa_id)
                                        ->get()
                                        ->pluck('user.name', 'id');
                                }
                                return [];
                            })
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('titulo')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('descripcion')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('estado')
                            ->options([
                                'abierta' => 'Abierta',
                                'en proceso' => 'En Proceso',
                                'resuelta' => 'Resuelta',
                            ])
                            ->default('abierta')
                            ->required(),
                        Forms\Components\Select::make('prioridad')
                            ->options([
                                'baja' => 'Baja',
                                'media' => 'Media',
                                'alta' => 'Alta',
                            ])
                            ->default('media')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Resolución')
                    ->schema([
                        Forms\Components\DateTimePicker::make('fecha_resolucion')
                            ->disabled(),
                        Forms\Components\Textarea::make('explicacion_resolucion')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record && $record->estado === 'resuelta'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('alumno.user.name')
                    ->label('Alumno')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('titulo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'abierta' => 'danger',
                        'en proceso' => 'warning',
                        'resuelta' => 'success',
                    }),
                Tables\Columns\TextColumn::make('prioridad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'baja' => 'info',
                        'media' => 'warning',
                        'alta' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('fecha_resolucion')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('estado')
                    ->options([
                        'abierta' => 'Abierta',
                        'en proceso' => 'En Proceso',
                        'resuelta' => 'Resuelta',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('resolver')
                    ->label('Resolver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->hidden(fn (Incidencia $record): bool => $record->estado === 'resuelta')
                    ->form([
                        Forms\Components\Textarea::make('explicacion_resolucion')
                            ->label('Explicación de la resolución')
                            ->required(),
                    ])
                    ->action(function (Incidencia $record, array $data): void {
                        $record->update([
                            'estado' => 'resuelta',
                            'fecha_resolucion' => now(),
                            'explicacion_resolucion' => $data['explicacion_resolucion'],
                        ]);
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Resolver Incidencia')
                    ->modalDescription('Por favor, indica cómo se ha resuelto la incidencia.')
                    ->modalSubmitActionLabel('Confirmar Resolución'),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
