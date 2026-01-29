<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlumnoResource\Pages;
use App\Models\Alumno;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Notifications\Notification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AlumnoResource extends Resource
{
    protected static ?string $model = Alumno::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Gestión Académica';

    protected static ?string $modelLabel = 'Alumno';

    protected static ?string $pluralModelLabel = 'Alumnos';

    /**
     * @brief Deshabilita la creación de alumnos desde este recurso.
     * La creación se centraliza en UserResource.
     * 
     * @return bool
     */
    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isTutorPracticas();
    }

    public static function canCreate(): bool
    {
        // La creación se centraliza en UserResource para mantener la integridad de la cuenta
        return auth()->user()->isAdmin() || auth()->user()->isTutorPracticas();
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isTutorPracticas();
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isTutorPracticas();
    }

    /**
     * @brief Obtiene el formulario configurado para el recurso Alumno.
     * 
     * @param Form $formulario Objeto del formulario.
     * @return Form Formulario configurado con campos de usuario, curso, empresa y tutores.
     */
    public static function form(Form $formulario): Form
    {
        return $formulario
            ->schema([
                Select::make('user_id')
                    ->label('Usuario')
                    ->placeholder('Selecciona un usuario')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('curso_id')
                    ->label('Curso')
                    ->placeholder('Selecciona un curso')
                    ->relationship('curso', 'nombre')
                    ->required(),
                Select::make('empresa_id')
                    ->label('Empresa')
                    ->placeholder('Selecciona una empresa')
                    ->relationship('empresa', 'nombre')
                    ->default(null),
                Select::make('tutor_curso_id')
                    ->relationship('tutorCurso', 'nombre')
                    ->label('Tutor de Curso')
                    ->placeholder('Asigna un tutor de curso')
                    ->default(null),
                Select::make('tutor_practicas_id')
                    ->relationship('tutorPracticas', 'nombre')
                    ->label('Tutor de Prácticas')
                    ->placeholder('Asigna un tutor de prácticas')
                    ->default(null),
            ]);
    }

    /**
     * @brief Obtiene la consulta base optimizada para el recurso Alumno.
     * 
     * @return Builder Consulta configurada con carga ansiosa y filtros por rol.
     */
    public static function getEloquentQuery(): Builder
    {
        $consulta = parent::getEloquentQuery()
            ->with(['user', 'curso', 'empresa', 'tutorCurso', 'tutorPracticas']);
            
        $usuarioActual = auth()->user();

        if ($usuarioActual->isAdmin()) {
            return $consulta;
        }

        if ($usuarioActual->isTutorCurso()) {
            return $consulta->where('tutor_curso_id', $usuarioActual->perfilTutorCurso?->id);
        }

        if ($usuarioActual->isAlumno()) {
            return $consulta->where('user_id', $usuarioActual->id);
        }

        if ($usuarioActual->isTutorPracticas()) {
            return $consulta->whereHas('tutorPracticas', fn($q) => $q->where('user_id', $usuarioActual->id));
        }

        return $consulta->whereRaw('1 = 0');
    }

    /**
     * @brief Obtiene la tabla configurada para el recurso Alumno.
     * 
     * @param Table $tabla Objeto de la tabla.
     * @return Table Tabla configurada con columnas, filtros y acciones.
     */
    public static function table(Table $tabla): Table
    {
        return $tabla
            ->deferLoading()
            ->headerActions([
                ExportAction::make()
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                            ->withFilename('alumnos_' . date('Y-m-d')),
                    ])
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn () => auth()->user()->isAdmin()),
            ])
            ->columns([
                ImageColumn::make('user.avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->state(fn (Alumno $record) => $record->user?->getFilamentAvatarUrl()),
                TextColumn::make('user.name')
                    ->label('Nombre Alumno')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('curso.nombre')
                    ->label('Curso')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('empresa.nombre')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('curso')
                    ->label('Filtrar por Curso')
                    ->relationship('curso', 'nombre'),
                SelectFilter::make('empresa')
                    ->label('Filtrar por Empresa')
                    ->relationship('empresa', 'nombre'),
            ])
            ->actions([
                Action::make('descargarInforme')
                    ->label('Descargar Informe')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function (Alumno $record) {
                        $pdf = Pdf::loadView('informe', ['alumno' => $record]);
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "informe_{$record->dni}.pdf"
                        );
                    }),
                EditAction::make()
                    ->label('Editar'),
                DeleteAction::make()
                    ->label('Eliminar')
                    ->successNotification(fn (Alumno $record) => 
                        Notification::make()
                            ->success()
                            ->title('Alumno eliminado')
                            ->body("El registro del alumno " . ($record->user?->name ?? 'desconocido') . " ha sido eliminado correctamente.")
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
                                ->title('Alumnos eliminados')
                                ->body("Los registros de los alumnos seleccionados han sido eliminados correctamente.")
                                ->sendToDatabase(auth()->user())
                        ),
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
            'index' => Pages\ListAlumnos::route('/'),
            'edit' => Pages\EditAlumno::route('/{record}/edit'),
        ];
    }
}
