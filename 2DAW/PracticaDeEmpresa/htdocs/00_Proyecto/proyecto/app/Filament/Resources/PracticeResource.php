<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PracticeResource\Pages;
use App\Models\Practice;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PracticeResource extends Resource
{
    protected static ?string $model = Practice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Tarea / Práctica';

    protected static ?string $pluralModelLabel = 'Tareas y Prácticas';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->isAdmin() || 
               auth()->user()->hasPermissionTo('gestionar_practicas') ||
               auth()->user()->hasRole('alumno');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || 
               auth()->user()->hasPermissionTo('gestionar_practicas') ||
               auth()->user()->hasRole('alumno');
    }

    /**
     * @brief Obtiene el formulario configurado para el recurso de Prácticas.
     * 
     * @param Form $formulario Objeto del formulario.
     * @return Form Formulario con secciones de asignación y detalles.
     */
    public static function form(Form $formulario): Form
    {
        return $formulario
            ->schema([
                Section::make('Asignación de la Práctica')
                    ->description('Indica quién puede ver esta práctica. Si no seleccionas Alumno, Grupo o Rol, la práctica será privada para ti.')
                    ->visible(fn () => !auth()->user()->isAlumno())
                    ->schema([
                        Select::make('alumno_id')
                            ->label('Alumno (Individual)')
                            ->placeholder('Selecciona un alumno')
                            ->relationship('alumno', 'dni')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record?->user?->name ?? $record?->dni ?? 'Sin nombre')
                            ->searchable()
                            ->preload()
                            ->hidden(fn () => auth()->user()->isAlumno())
                            ->dehydrated(fn ($state) => filled($state) || auth()->user()->isAlumno()),

                        Select::make('curso_id')
                            ->label('Grupo / Curso (Compartida)')
                            ->placeholder('Selecciona un curso')
                            ->relationship('curso', 'nombre')
                            ->searchable()
                            ->preload()
                            ->visible(fn () => auth()->user()->isAdmin() || auth()->user()->isTutorCurso()),

                        Select::make('target_role')
                            ->label('Rol Destinatario (Compartida)')
                            ->placeholder('Selecciona un rol')
                            ->options([
                                'alumno' => 'Todos los Alumnos',
                                'tutor_practicas' => 'Todos los Tutores de Empresa',
                                'tutor_curso' => 'Todos los Tutores de Prácticas (Centro)',
                            ])
                            ->visible(fn () => auth()->user()->isAdmin() || auth()->user()->isTutorCurso()),
                    ])->columns(3),

                Section::make('Detalles de la Práctica')
                    ->description('Información sobre el contenido y fechas de la práctica.')
                    ->schema([
                        TextInput::make('title')
                            ->label('Título')
                            ->placeholder('Ej: Memoria de actividades semana 1')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Descripción')
                            ->placeholder('Describe brevemente el contenido o instrucciones...')
                            ->columnSpanFull(),
                        DateTimePicker::make('starts_at')
                            ->label('Fecha de Inicio')
                            ->placeholder('Selecciona fecha y hora')
                            ->required(),
                        DateTimePicker::make('ends_at')
                            ->label('Fecha de Fin')
                            ->placeholder('Selecciona fecha y hora')
                            ->required(),
                        FileUpload::make('attachments')
                            ->label('Documentos Adjuntos')
                            ->multiple()
                            ->disk('public')
                            ->directory('practices')
                            ->downloadable()
                            ->openable()
                            ->columnSpanFull(),
                        Hidden::make('user_id')
                            ->default(fn () => auth()->id()),
                    ])->columns(2),
            ]);
    }

    /**
     * @brief Obtiene la tabla configurada para el recurso de Prácticas.
     * 
     * @param Table $tabla Objeto de la tabla.
     * @return Table Tabla configurada con columnas de visibilidad y destinatarios.
     */
    public static function table(Table $tabla): Table
    {
        return $tabla
            ->deferLoading()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable(isIndividual: false, isGlobal: true)
                    ->sortable(),
                TextColumn::make('tipo_visibilidad')
                    ->label('Visibilidad')
                    ->badge()
                    ->getStateUsing(function (Practice $record) {
                        if ($record->alumno_id) return 'Individual';
                        if ($record->curso_id) return 'Grupo: ' . $record->curso->nombre;
                        if ($record->target_role) return 'Rol: ' . $record->target_role;
                        return 'Privada';
                    })
                    ->color(fn (string $state): string => match (true) {
                        str_contains($state, 'Individual') => 'info',
                        str_contains($state, 'Grupo') => 'warning',
                        str_contains($state, 'Rol') => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('alumno.user.name')
                    ->label('Destinatario')
                    ->placeholder('Compartida / Global')
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label('Inicio')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Fin')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label('Creado por')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('curso_id')
                    ->label('Filtrar por Grupo')
                    ->relationship('curso', 'nombre'),
                SelectFilter::make('target_role')
                    ->label('Filtrar por Rol')
                    ->options([
                        'alumno' => 'Alumnos',
                        'tutor_practicas' => 'Tutores Empresa',
                        'tutor_curso' => 'Tutores Prácticas',
                    ]),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Ver detalles'),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->hidden(fn () => auth()->user()->isAlumno() || auth()->user()->isTutorPracticas()),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label('Eliminar seleccionados')
                        ->hidden(fn () => auth()->user()->isAlumno() || auth()->user()->isTutorPracticas()),
                ])->label('Acciones por lote'),
            ])
            ->headerActions([]);
    }

    /**
     * @brief Obtiene la consulta base optimizada para el recurso de Prácticas.
     * 
     * @return Builder Consulta filtrada por visibilidad y pertenencia.
     */
    public static function getEloquentQuery(): Builder
    {
        $usuarioActual = auth()->user();
        $consulta = parent::getEloquentQuery()->with(['alumno.user', 'curso', 'creator']);

        if ($usuarioActual->isAdmin()) {
            return $consulta;
        }

        return $consulta->where(function (Builder $subconsulta) use ($usuarioActual) {
            // 1. Lo que yo he creado
            $subconsulta->where('user_id', $usuarioActual->id);

            // 2. Si soy Alumno, ver lo asignado a mí, a mi grupo o a todos los alumnos
            if ($usuarioActual->isAlumno()) {
                $alumnoId = $usuarioActual->alumno?->id;
                $cursoId = $usuarioActual->alumno?->curso_id;

                $subconsulta->orWhere('alumno_id', $alumnoId)
                  ->orWhere('curso_id', $cursoId)
                  ->orWhere('target_role', 'alumno');
            }

            // 3. Si soy Tutor de Empresa, ver lo asignado a mis alumnos o a todos los tutores de empresa
            if ($usuarioActual->isTutorPracticas()) {
                $subconsulta->orWhereHas('alumno', function ($q) use ($usuarioActual) {
                    $q->whereHas('tutorPracticas', function ($tutorQ) use ($usuarioActual) {
                        $tutorQ->where('user_id', $usuarioActual->id);
                    });
                })
                ->orWhere('target_role', 'tutor_practicas');
            }

            // 4. Si soy Tutor de Curso, ver lo de mis grupos o a todos los tutores de curso
            if ($usuarioActual->isTutorCurso()) {
                $subconsulta->orWhere('target_role', 'tutor_curso');
            }
        });
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return !auth()->user()->isAlumno();
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->isAdmin() || $record->user_id === auth()->id();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPractices::route('/'),
        ];
    }
}
