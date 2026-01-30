<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class BackupStatusWidget extends BaseWidget
{
    protected static ?int $sort = -5; // Aparecerá de los primeros

    protected function getStats(): array
    {
        // El backup está programado para el día 1 de cada mes a las 00:00
        $proximoBackup = Carbon::now()->addMonthNoOverflow()->startOfMonth();
        
        // Si hoy es día 1 y aún no han pasado las 00:00 (caso raro en ejecución)
        if (Carbon::now()->day === 1 && Carbon::now()->hour === 0) {
            $proximoBackup = Carbon::now()->startOfDay();
        } else if (Carbon::now()->day === 1) {
            // Ya pasó el del día 1 de este mes
            $proximoBackup = Carbon::now()->addMonthNoOverflow()->startOfMonth();
        } else {
            $proximoBackup = Carbon::now()->startOfMonth()->addMonth();
        }

        $diasRestantes = floor(Carbon::now()->diffInDays($proximoBackup));
        
        // Intentar obtener la fecha del último backup realizado
        $ultimoBackup = "No disponible";
        $color = 'warning';
        
        $disks = config('backup.backup.destination.disks', ['public']);
        foreach ($disks as $diskName) {
            $files = Storage::disk($diskName)->allFiles();
            // Filtrar archivos .zip (que suelen ser los backups de spatie)
            $backupFiles = array_filter($files, fn($file) => str_ends_with($file, '.zip'));
            
            if (!empty($backupFiles)) {
                // Obtener el más reciente por tiempo de modificación
                $latestFile = collect($backupFiles)
                    ->map(fn($file) => [
                        'name' => $file,
                        'time' => Storage::disk($diskName)->lastModified($file)
                    ])
                    ->sortByDesc('time')
                    ->first();
                
                if ($latestFile) {
                    $ultimoBackup = Carbon::createFromTimestamp($latestFile['time'])->diffForHumans();
                    $color = 'success';
                    break;
                }
            }
        }

        return [
            Stat::make('Próximo Backup Automático', $proximoBackup->format('d/m/Y'))
                ->description("Faltan {$diasRestantes} días (Mensual)")
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
            Stat::make('Último Backup Realizado', $ultimoBackup)
                ->description('Estado del sistema de respaldo')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color($color),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->hasPermissionTo('gestionar_backups');
    }
}
