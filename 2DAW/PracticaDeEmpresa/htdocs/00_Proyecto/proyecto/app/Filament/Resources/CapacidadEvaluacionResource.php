<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CapacidadEvaluacionResource\Pages;
use App\Models\CapacidadEvaluacion;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CapacidadEvaluacionResource extends Resource
{
    protected static ?string $model = CapacidadEvaluacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Configuración Académica';

    protected static ?string $modelLabel = 'Capacidad';

    protected static ?string $pluralModelLabel = 'Capacidades';

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
     * @brief Obtiene la consulta base optimizada para el recurso Capacidad de Evaluación.
     * 
     * @return Builder Consulta configurada con carga ansiosa y sin scopes globales de borrado lógico.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['perteneceACriterio'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * @brief Obtiene el formulario configurado para el recurso Capacidad de Evaluación.
     * 
     * @param Form $formulario Objeto del formulario.
     * @return Form Formulario configurado con relación a criterios, nombre y puntuación.
     */
    public static function form(Form $formulario): Form
    {
        return $formulario
            ->schema([
                Section::make('Definición de Capacidad')
                    ->description('Detalles de la competencia o capacidad específica a evaluar.')
                    ->schema([
                        Select::make('criterio_id')
                            ->label('Criterio General')
                            ->placeholder('Selecciona un criterio')
                            ->relationship('perteneceACriterio', 'nombre')
                            ->required(),
                        TextInput::make('nombre')
                            ->label('Nombre de la Capacidad')
                            ->placeholder('Ej: Capacidad de aprendizaje')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('puntuacion_maxima')
                            ->label('Puntuación Máxima')
                            ->placeholder('Ej: 10')
                            ->numeric()
                            ->default(10)
                            ->required(),
                        Toggle::make('activo')
                            ->label('Capacidad Activa')
                            ->helperText('Indica si esta capacidad está disponible para evaluar.')
                            ->default(true)
                            ->required(),
                        Textarea::make('descripcion')
                            ->label('Descripción Detallada')
                            ->placeholder('Detalla qué aspectos se valoran en esta capacidad...')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    /**
     * @brief Obtiene la tabla configurada para el recurso Capacidad de Evaluación.
     * 
     * @param Table $tabla Objeto de la tabla.
     * @return Table Tabla configurada con columnas de criterio, nombre y estado.
     */
    public static function table(Table $tabla): Table
    {
        return $tabla
            ->deferLoading()
            ->columns([
                TextColumn::make('perteneceACriterio.nombre')
                    ->label('Criterio Padre')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nombre')
                    ->label('Capacidad / Competencia')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('puntuacion_maxima')
                    ->label('Máx. Puntos')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('activo')
                    ->label('Activa')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creada el')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make()
                    ->label('Ver eliminadas')
                    ->visible(fn() => auth()->user()->isAdmin()),
                SelectFilter::make('criterio_id')
                    ->label('Filtrar por Criterio')
                    ->relationship('perteneceACriterio', 'nombre'),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Ver'),
                EditAction::make()
                    ->label('Editar'),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->successNotification(fn (CapacidadEvaluacion $record) => 
                        Notification::make()
                            ->success()
                            ->title('Capacidad eliminada')
                            ->body("La capacidad {$record->nombre} ha sido eliminada correctamente.")
                            ->sendToDatabase(auth()->user())
                    ),
                RestoreAction::make()
                    ->label('Restaurar'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionadas')
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Capacidades eliminadas')
                                ->body("Las capacidades seleccionadas han sido eliminadas correctamente.")
                                ->sendToDatabase(auth()->user())
                        ),
                    RestoreBulkAction::make()
                        ->label('Restaurar seleccionadas'),
                    ForceDeleteBulkAction::make()
                        ->label('Borrado permanente'),
                ])->label('Acciones por lote'),
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
            'index' => Pages\ListCapacidadEvaluacions::route('/'),
            'create' => Pages\CreateCapacidadEvaluacion::route('/create'),
            'view' => Pages\ViewCapacidadEvaluacion::route('/{record}'),
            'edit' => Pages\EditCapacidadEvaluacion::route('/{record}/edit'),
        ];
    }
}
