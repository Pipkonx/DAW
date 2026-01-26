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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ObservacionDiariaResource extends Resource
{
    protected static ?string $model = ObservacionDiaria::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';

    protected static ?string $navigationGroup = 'Gestión Académica';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('alumno_id')
                    ->relationship('alumno', 'id')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name)
                    ->searchable(['user.name'])
                    ->required(),
                Forms\Components\DatePicker::make('fecha')
                    ->required(),
                Forms\Components\TextInput::make('horas')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('actividad')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('observaciones')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('alumno.user.name')
                    ->label('Alumno')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('horas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('actividad')
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
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->user->name),
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
            'index' => Pages\ListObservacionDiarias::route('/'),
            'create' => Pages\CreateObservacionDiaria::route('/create'),
            'edit' => Pages\EditObservacionDiaria::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->isAdmin() || $user->isTutorCurso()) {
            return $query;
        }

        if ($user->isAlumno()) {
            return $query->whereHas('alumno', fn($q) => $q->where('user_id', $user->id));
        }

        if ($user->isTutorEmpresa()) {
            return $query->whereHas('alumno', fn($q) => $q->where('empresa_id', $user->empresa_id));
        }

        return $query->whereRaw('1 = 0'); // No debería ver nada si no cumple lo anterior
    }
}
