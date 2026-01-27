<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlumnoResource\Pages;
use App\Filament\Resources\AlumnoResource\RelationManagers;
use App\Models\Alumno;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
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
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('curso_id')
                    ->relationship('curso', 'nombre')
                    ->required(),
                Forms\Components\Select::make('empresa_id')
                    ->relationship('empresa', 'nombre')
                    ->default(null),
                Forms\Components\Select::make('tutor_curso_id')
                    ->relationship('tutorCurso', 'nombre')
                    ->label('Tutor de Curso')
                    ->default(null),
                Forms\Components\Select::make('tutor_practicas_id')
                    ->relationship('tutorPracticas', 'nombre')
                    ->label('Tutor de Prácticas')
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
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
                Tables\Columns\ImageColumn::make('user.avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->user->name) . '&color=FFFFFF&background=111827'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nombre Alumno')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('curso.nombre')
                    ->label('Curso')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('empresa.nombre')
                    ->label('Empresa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('curso')
                    ->relationship('curso', 'nombre'),
                Tables\Filters\SelectFilter::make('empresa')
                    ->relationship('empresa', 'nombre'),
            ])
            ->actions([
                Tables\Actions\Action::make('descargarInforme')
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAlumnos::route('/'),
            'edit' => Pages\EditAlumno::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->isAlumno()) {
            return $query->where('user_id', $user->id);
        }

        if ($user->isTutorEmpresa()) {
            return $query->where('empresa_id', $user->empresa_id);
        }

        return $query;
    }
}
