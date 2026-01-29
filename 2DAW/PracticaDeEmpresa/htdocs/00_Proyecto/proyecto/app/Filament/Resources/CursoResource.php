<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CursoResource\Pages;
use App\Filament\Resources\CursoResource\RelationManagers;
use App\Models\Curso;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CursoResource extends Resource
{
    protected static ?string $model = Curso::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Gestión Académica';

    protected static ?string $modelLabel = 'Curso';

    protected static ?string $pluralModelLabel = 'Cursos';

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isTutorCurso();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isTutorCurso()) {
            return $query->where('tutor_curso_id', $user->perfilTutorCurso?->id);
        }

        return $query->whereRaw('1 = 0');
    }

    /**
     * @brief Configura el formulario para el recurso Curso.
     * 
     * @param Form $formulario Instancia del formulario.
     * @return Form Formulario configurado con validaciones de fechas y relaciones.
     */
    public static function form(Form $formulario): Form
    {
        return $formulario
            ->schema([
                Forms\Components\Section::make('Información del Curso')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre del Curso')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->maxLength(65535),
                        Forms\Components\TextInput::make('duracion')
                            ->label('Duración (Horas)')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        Forms\Components\Select::make('tutor_curso_id')
                            ->label('Tutor del Curso')
                            ->relationship('tutorCurso', 'nombre')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Periodo Académico')
                    ->schema([
                        Forms\Components\DatePicker::make('fecha_inicio')
                            ->label('Fecha de Inicio')
                            ->required()
                            ->live(),
                        Forms\Components\DatePicker::make('fecha_fin')
                            ->label('Fecha de Finalización')
                            ->required()
                            ->afterOrEqual('fecha_inicio')
                            ->validationMessages([
                                'after_or_equal' => 'La fecha de fin no puede ser anterior a la fecha de inicio.',
                            ]),
                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true)
                            ->required(),
                    ])->columns(2),
            ]);
    }

    /**
     * @brief Configura la tabla para el recurso Curso.
     * 
     * @param Table $tabla Instancia de la tabla.
     * @return Table Tabla configurada con columnas de relación y acciones.
     */
    public static function table(Table $tabla): Table
    {
        return $tabla
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Curso')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tutorCurso.nombre')
                    ->label('Tutor Asignado')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duracion')
                    ->label('Horas')
                    ->suffix(' h')
                    ->sortable(),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(fn (Curso $record) => 
                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Curso eliminado')
                            ->body("El curso {$record->nombre} ha sido eliminado correctamente.")
                            ->sendToDatabase(\Filament\Facades\Filament::auth()->user())
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Cursos eliminados')
                                ->body("Los cursos seleccionados han sido eliminados correctamente.")
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
            'index' => Pages\ListCursos::route('/'),
            'create' => Pages\CreateCurso::route('/create'),
            'edit' => Pages\EditCurso::route('/{record}/edit'),
        ];
    }
}
