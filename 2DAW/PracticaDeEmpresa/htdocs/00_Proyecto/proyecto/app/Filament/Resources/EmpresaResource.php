<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmpresaResource\Pages;
use App\Filament\Resources\EmpresaResource\RelationManagers;
use App\Models\Empresa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmpresaResource extends Resource
{
    protected static ?string $model = Empresa::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Gestión Académica';

    /**
     * @brief Configura el formulario para el recurso Empresa.
     * 
     * @param Form $formulario Instancia del formulario.
     * @return Form Formulario configurado.
     */
    public static function form(Form $formulario): Form
    {
        return $formulario
            ->schema([
                Forms\Components\Section::make('Información de la Empresa')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre de la Empresa')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('cif')
                            ->label('CIF')
                            ->required()
                            ->regex('/^[ABCDEFGHJNPQRSUVW][0-9]{7}[0-9A-J]$/')
                            ->validationMessages([
                                'regex' => 'El formato del CIF no es válido.',
                            ]),
                        Forms\Components\TextInput::make('sector')
                            ->label('Sector de Actividad')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('activo')
                            ->label('Empresa Activa')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Contacto y Ubicación')
                    ->schema([
                        Forms\Components\TextInput::make('direccion')
                            ->label('Dirección')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('persona_contacto')
                            ->label('Persona de Contacto')
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }

    /**
     * @brief Configura la tabla para el recurso Empresa.
     * 
     * @param Table $tabla Instancia de la tabla.
     * @return Table Tabla configurada con columnas, filtros y acciones.
     */
    public static function table(Table $tabla): Table
    {
        return $tabla
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cif')
                    ->label('CIF')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sector')
                    ->label('Sector')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('persona_contacto')
                    ->label('Contacto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('activo')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Inactivo')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_creacion')
                    ->label('Fecha Alta')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('sector')
                    ->label('Filtrar por Sector')
                    ->options(fn () => Empresa::pluck('sector', 'sector')->filter()->toArray()),
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Estado Activo'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmpresas::route('/'),
            'create' => Pages\CreateEmpresa::route('/create'),
            'edit' => Pages\EditEmpresa::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->isAdmin() || $user->isTutorCurso()) {
            return $query;
        }

        if ($user->isTutorEmpresa()) {
            return $query->where('id', $user->empresa_id);
        }

        return $query->whereRaw('1 = 0');
    }
}
