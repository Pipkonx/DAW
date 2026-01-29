<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Notifications\Notification;

/**
 * @class AccionesPrincipales
 * @brief Widget personalizado para mostrar botones de acceso rápido según el rol del usuario.
 */
class AccionesPrincipales extends Widget
{
    protected static string $view = 'filament.widgets.acciones-principales';

    protected int | string | array $columnSpan = 'full';

    /**
     * @brief Envía una notificación de prueba al usuario actual.
     */
    public function enviarNotificacionPrueba(): void
    {
        Notification::make()
            ->title('Notificación de Prueba')
            ->body('Si estás viendo esto, la tabla de notificaciones funciona correctamente.')
            ->success()
            ->icon('heroicon-o-check-circle')
            ->sendToDatabase(auth()->user());
        
        Notification::make()
            ->title('¡Éxito!')
            ->success()
            ->send();
    }

    /**
     * @brief Determina las acciones disponibles basadas en el rol del usuario.
     * 
     * @return array Lista de acciones con etiqueta, icono, url y color.
     */
    protected function obtenerAcciones(): array
    {
        $usuario = auth()->user();
        $rol = $usuario->getRoleNames()->first();

        return match ($rol) {
            'alumno' => [
                [
                    'etiqueta' => 'Registrar Jornada Diaria',
                    'icono' => 'heroicon-m-plus-circle',
                    'url' => '/admin/observacion-diarias/create',
                    'color' => 'primary',
                ],
            ],
            'tutor_curso' => [
                [
                    'etiqueta' => 'Evaluar Alumno',
                    'icono' => 'heroicon-m-clipboard-document-check',
                    'url' => '/admin/evaluacions/create',
                    'color' => 'success',
                ],
                [
                    'etiqueta' => 'Descargar Listado PDF',
                    'icono' => 'heroicon-m-arrow-down-tray',
                    'url' => '#', // Implementar ruta de descarga si existe
                    'color' => 'info',
                ],
            ],
            'admin' => [
                [
                    'etiqueta' => 'Crear Nuevo Usuario',
                    'icono' => 'heroicon-m-user-plus',
                    'url' => '/admin/users/create',
                    'color' => 'primary',
                ],
                [
                    'etiqueta' => 'Configurar Curso',
                    'icono' => 'heroicon-m-cog-6-tooth',
                    'url' => '/admin/cursos',
                    'color' => 'gray',
                ],
            ],
            'tutor_practicas' => [],
            default => [],
        };
    }

    /**
     * @brief Pasa las acciones a la vista del widget.
     * 
     * @return array Datos para la vista.
     */
    protected function getViewData(): array
    {
        return [
            'acciones' => $this->obtenerAcciones(),
        ];
    }
}
