<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PracticeResource\Pages;
use App\Filament\Resources\PracticeResource\RelationManagers;
use App\Models\Practice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PracticeResource extends Resource
{
    protected static ?string $model = Practice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Asignación de la Práctica')
                    ->description('Indica quién puede ver esta práctica. Si no seleccionas Alumno, Grupo o Rol, la práctica será privada para ti.')
                    ->visible(fn () => !auth()->user()->isAlumno())
                    ->schema([
                        Forms\Components\Select::make('alumno_id')
                            ->label('Alumno (Individual)')
                            ->relationship('alumno', 'dni')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record?->user?->name ?? $record?->dni ?? 'Sin nombre')
                            ->searchable()
                            ->preload()
                            ->hidden(fn () => auth()->user()->isAlumno())
                            ->dehydrated(fn ($state) => filled($state) || auth()->user()->isAlumno()),

                        Forms\Components\Select::make('curso_id')
                            ->label('Grupo / Curso (Compartida)')
                            ->relationship('curso', 'nombre')
                            ->searchable()
                            ->preload()
                            ->visible(fn () => auth()->user()->isAdmin() || auth()->user()->isTutorCurso()),

                        Forms\Components\Select::make('target_role')
                            ->label('Rol Destinatario (Compartida)')
                            ->options([
                                'alumno' => 'Todos los Alumnos',
                                'tutor_practicas' => 'Todos los Tutores de Empresa',
                                'tutor_curso' => 'Todos los Tutores de Prácticas (Centro)',
                            ])
                            ->visible(fn () => auth()->user()->isAdmin() || auth()->user()->isTutorCurso()),
                    ])->columns(3),

                Forms\Components\Section::make('Detalles de la Práctica')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Título')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('starts_at')
                            ->label('Inicio')
                            ->required(),
                        Forms\Components\DateTimePicker::make('ends_at')
                            ->label('Fin')
                            ->required(),
                        Forms\Components\FileUpload::make('attachments')
                            ->label('Adjuntos')
                            ->multiple()
                            ->disk('public')
                            ->directory('practices')
                            ->columnSpanFull(),
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => auth()->id()),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable(isIndividual: false, isGlobal: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_visibilidad')
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
                Tables\Columns\TextColumn::make('alumno.user.name')
                    ->label('Destinatario')
                    ->placeholder('Compartida / Global')
                    ->sortable(),
                Tables\Columns\TextColumn::make('starts_at')
                    ->label('Inicio')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->label('Fin')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Creado por')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('curso_id')
                    ->label('Por Grupo')
                    ->relationship('curso', 'nombre'),
                Tables\Filters\SelectFilter::make('target_role')
                    ->label('Por Rol')
                    ->options([
                        'alumno' => 'Alumnos',
                        'tutor_practicas' => 'Tutores Empresa',
                        'tutor_curso' => 'Tutores Prácticas',
                    ]),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn () => auth()->user()->isAlumno() || auth()->user()->isTutorPracticas()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden(fn () => auth()->user()->isAlumno() || auth()->user()->isTutorPracticas()),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery()->with(['alumno.user', 'curso', 'creator']);

        if ($user->isAdmin()) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($user) {
            // 1. Lo que yo he creado
            $q->where('user_id', $user->id);

            // 2. Si soy Alumno, ver lo asignado a mí, a mi grupo o a todos los alumnos
            if ($user->isAlumno()) {
                $alumnoId = $user->alumno?->id;
                $cursoId = $user->alumno?->curso_id;

                $q->orWhere('alumno_id', $alumnoId)
                  ->orWhere('curso_id', $cursoId)
                  ->orWhere('target_role', 'alumno');
            }

            // 3. Si soy Tutor de Empresa, ver lo asignado a mis alumnos o a todos los tutores de empresa
            if ($user->isTutorPracticas()) {
                $q->orWhereHas('alumno', function ($subQ) use ($user) {
                    $subQ->whereHas('tutorPracticas', function ($tutorQ) use ($user) {
                        $tutorQ->where('user_id', $user->id);
                    });
                })
                ->orWhere('target_role', 'tutor_practicas');
            }

            // 4. Si soy Tutor de Curso, ver lo de mis grupos o a todos los tutores de curso
            if ($user->isTutorCurso()) {
                // Asumimos que ven todo lo de su rol o lo que ellos crean (ya cubierto por user_id)
                $q->orWhere('target_role', 'tutor_curso');
                
                // Si tienen cursos asignados (habría que ver la relación, por ahora permitimos ver lo que sea para 'tutor_curso')
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
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPractices::route('/'),
        ];
    }
}
