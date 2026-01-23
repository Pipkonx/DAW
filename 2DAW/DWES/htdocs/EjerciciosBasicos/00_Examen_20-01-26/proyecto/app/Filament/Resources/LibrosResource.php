<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LibrosResource\Pages;
use App\Filament\Resources\LibrosResource\RelationManagers;
use App\Models\Libros;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;

use Filament\Forms\Components\Select; // No olvides este import arriba
use Filament\Tables\Filters\SelectFilter;



class LibrosResource extends Resource
{
    protected static ?string $model = Libros::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. Selector de Usuario (Relación)
                Select::make('user_id')
                    ->relationship('user', 'name') // 'user' es el nombre de la función en el modelo
                    ->label('Asignar a un Autor')
                    ->searchable()
                    ->preload()
                    ->required(),

                // 2. Campo de titulo
                TextInput::make('titulo')
                    ->label('Titulo')
                    ->required()
                    ->maxLength(255),

                // 3. Campo de fecha de publicacion
                DatePicker::make('fecha_publicacion')
                    ->displayFormat('d/m/Y')
                    ->timezone('Europe/Madrid')
                    ->label('Fecha de Publicación')
                    ->required()
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 4. Columna de Usuario (Relación)
                TextColumn::make('user.name')->label('Autor')->sortable()->searchable(),
                TextColumn::make('titulo')->sortable()->searchable(),
                TextColumn::make('fecha_publicacion')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                // 5. Añadimos el filtro por la relacion
                SelectFilter::make('user_id')
                    ->label('Filtrar por Autor')
                    ->relationship('user', 'name'),
            ])
            ->actions([
                \Filament\Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListLibros::route('/'),
            'create' => Pages\CreateLibros::route('/create'),
            'edit' => Pages\EditLibros::route('/{record}/edit'),
        ];
    }
}
