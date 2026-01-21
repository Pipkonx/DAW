<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Select; // No olvides este import arriba
use Filament\Tables\Filters\SelectFilter;



class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 1. Selector de Usuario (Relación)
                Select::make('user_id')
                    ->relationship('user', 'name') // 'user' es el nombre de la función en el modelo
                    ->label('Asignar a Usuario')
                    ->searchable()
                    ->preload()
                    ->required(),

                // 2. Campo Nombre
                TextInput::make('name')
                    ->label('Nombre del Producto')
                    ->required()
                    ->maxLength(255),

                // 3. Campo Descripción
                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(3),

                // 4. Campo Precio
                TextInput::make('price')
                    ->label('Precio')
                    ->numeric()
                    ->prefix('€')
                    ->required(),
            ]);
    }


public static function table(Table $table): Table
{
    return $table
        ->columns([
            // 1. Añadimos la columna de la relación (Muestra el nombre del Usuario)
            TextColumn::make('user.name')
                ->label('Propietario')
                ->sortable()
                ->searchable(),

            TextColumn::make('name')
                ->label('Producto')
                ->sortable()
                ->searchable(),

            TextColumn::make('price')
                ->label('Precio')
                ->money('eur')
                ->sortable(),

            TextColumn::make('created_at')
                ->label('Fecha')
                ->dateTime()
                ->sortable(),
        ])
        ->filters([
            // 2. Añadimos el filtro por relación (Cumple el requisito de filtrar)
            SelectFilter::make('user_id')
                ->label('Filtrar por Propietario')
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


    // public static function table(Table $table): Table
    // {
    //     return $table
    //         ->columns([
    //             TextColumn::make('name')->sortable()->searchable(),
    //             TextColumn::make('price')->money('eur')->sortable(),
    //             TextColumn::make('created_at')->label('Fecha')->dateTime(),
    //         ])
    //         ->filters([
    //             //
    //         ])
    //         ->actions([
    //             Tables\Actions\EditAction::make(),
    //         ])
    //         ->bulkActions([
    //             Tables\Actions\BulkActionGroup::make([
    //                 Tables\Actions\DeleteBulkAction::make(),
    //             ]),
    //         ]);
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
