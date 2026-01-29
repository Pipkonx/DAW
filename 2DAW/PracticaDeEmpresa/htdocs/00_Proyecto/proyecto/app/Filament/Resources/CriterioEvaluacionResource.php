<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CriterioEvaluacionResource\Pages;
use App\Models\CriterioEvaluacion;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CriterioEvaluacionResource extends Resource
{
    protected static ?string $model = CriterioEvaluacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    protected static ?string $navigationGroup = 'Configuración Académica';

    protected static ?string $modelLabel = 'Criterio';

    protected static ?string $pluralModelLabel = 'Criterios';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->isAdmin() || 
               auth()->user()->isTutorPracticas() || 
               auth()->user()->hasPermissionTo('gestionar_capacidades');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || 
               auth()->user()->isTutorPracticas() || 
               auth()->user()->hasPermissionTo('gestionar_capacidades');
    }

    /**
     * @brief Obtiene el formulario configurado para el recurso Criterio de Evaluación.
     * 
     * @param Form $formulario Objeto del formulario.
     * @return Form Formulario configurado con nombre, peso y estado.
     */
    public static function form(Form $formulario): Form
    {
        return $formulario
            ->schema([
                Section::make('Detalles del Criterio')
                    ->description('Configuración del criterio de evaluación y su importancia relativa.')
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre del Criterio')
                            ->placeholder('Ej: Actitud y puntualidad')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('peso')
                            ->label('Peso en la Evaluación (%)')
                            ->placeholder('Ej: 20')
                            ->numeric()
                            ->step(0.01)
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->nullable(),
                        Toggle::make('activo')
                            ->label('Criterio Activo')
                            ->helperText('Indica si este criterio se utiliza en las nuevas evaluaciones.')
                            ->default(true)
                            ->required(),
                        Textarea::make('descripcion')
                            ->label('Descripción Detallada')
                            ->placeholder('Explica qué se evalúa en este criterio...')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    /**
     * @brief Obtiene la tabla configurada para el recurso Criterio de Evaluación.
     * 
     * @param Table $tabla Objeto de la tabla.
     * @return Table Tabla configurada con columnas de nombre, peso y estado activo.
     */
    public static function table(Table $tabla): Table
    {
        return $tabla
            ->deferLoading()
            ->columns([
                TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('peso')
                    ->label('Peso (%)')
                    ->suffix('%')
                    ->sortable(),
                IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado el')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make()
                    ->label('Ver eliminados')
                    ->visible(fn() => auth()->user()->isAdmin()),
                TernaryFilter::make('activo')
                    ->label('Solo activos'),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Ver'),
                EditAction::make()
                    ->label('Editar'),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->successNotification(fn (CriterioEvaluacion $record) => 
                        Notification::make()
                            ->success()
                            ->title('Criterio eliminado')
                            ->body("El criterio {$record->nombre} ha sido eliminado correctamente.")
                            ->sendToDatabase(auth()->user())
                    ),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Criterios eliminados')
                                ->body("Los criterios seleccionados han sido eliminados correctamente.")
                                ->sendToDatabase(auth()->user())
                        ),
                    RestoreBulkAction::make()
                        ->label('Restaurar seleccionados'),
                    ForceDeleteBulkAction::make()
                        ->label('Borrado permanente'),
                ])->label('Acciones por lote'),
            ]);
    }

    /**
     * @brief Obtiene la consulta base para el recurso Criterio de Evaluación.
     * 
     * @return Builder Consulta configurada sin filtros globales de soft delete.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
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
            'index' => Pages\ListCriterioEvaluacions::route('/'),
            'create' => Pages\CreateCriterioEvaluacion::route('/create'),
            'view' => Pages\ViewCriterioEvaluacion::route('/{record}'),
            'edit' => Pages\EditCriterioEvaluacion::route('/{record}/edit'),
        ];
    }
}
