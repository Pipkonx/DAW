<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

// Importaciones necesarias para que no dé error
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    // Cambiamos el icono a uno de carrito o caja
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Sección de Relaciones
                Section::make('Asociaciones')
                    ->description('Vincule este producto con un usuario y una categoría.')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Propietario/Usuario')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('category_id')
                            ->relationship('category', 'name')
                            ->label('Categoría')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                // Sección de Datos del Producto
                Section::make('Información del Producto')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del Producto')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('price')
                            ->label('Precio')
                            ->numeric()
                            ->prefix('€')
                            ->required(),

                        Textarea::make('description')
                            ->label('Descripción')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Producto')
                    ->sortable()
                    ->searchable(),

                // Mostramos el nombre de la categoría usando la relación
                TextColumn::make('category.name')
                    ->label('Categoría')
                    ->sortable()
                    ->badge() // Le da un estilo visual de etiqueta
                    ->color('success'),

                // Mostramos el nombre del usuario
                TextColumn::make('user.name')
                    ->label('Vendedor')
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Precio')
                    ->money('eur')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Aquí podrías añadir un filtro por categoría más adelante
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            // Aquí irían RelationManagers si quisieras ver productos dentro de Categorías
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