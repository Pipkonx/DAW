<?php

namespace App\Filament\Resources\IncidenciaResource\Pages;

use App\Filament\Resources\IncidenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Incidencia;
use Filament\Forms;
use Filament\Notifications\Notification;

/**
 * @class ViewIncidencia
 * @brief Página para visualizar una Incidencia.
 */
class ViewIncidencia extends ViewRecord
{
    protected static string $resource = IncidenciaResource::class;

    /**
     * @brief Define las acciones de la cabecera en la página de visualización.
     * 
     * @return array Lista de acciones disponibles.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('marcarComoResuelta')
                 ->label('Resolver')
                 ->icon('heroicon-o-check-circle')
                 ->color('success')
                 ->disabled(fn (Incidencia $record): bool => $record->estado === 'RESUELTA')
                 ->form([
                    Forms\Components\Textarea::make('resolucion')
                        ->label('Explicación de la resolución')
                        ->required(),
                ])
                ->action(function (Incidencia $record, array $data): void {
                    $record->update([
                        'estado' => 'RESUELTA',
                        'fecha_resolucion' => now(),
                        'resolucion' => $data['resolucion'],
                    ]);
                })
                ->successNotification(fn (Incidencia $record) => 
                    Notification::make()
                        ->success()
                        ->title('Incidencia resuelta')
                        ->body("La incidencia del alumno " . ($record->alumno?->user?->name ?? 'desconocido') . " ha sido marcada como resuelta.")
                        ->sendToDatabase(\Filament\Facades\Filament::auth()->user())
                )
                ->modalHeading('Resolver Incidencia')
                ->modalDescription('Por favor, indica cómo se ha resuelto la incidencia.')
                ->modalSubmitActionLabel('Confirmar Resolución'),
            Actions\EditAction::make(),
        ];
    }
}
