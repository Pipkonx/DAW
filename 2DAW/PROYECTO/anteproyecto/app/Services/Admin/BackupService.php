<?php

namespace App\Services\Admin;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\UploadedFile;

/**
 * BackupService - Sistema de resiliencia y recuperación de Pipkonx.
 * 
 * Centraliza la lógica de copias de seguridad de la base de datos SQLite, 
 * permitiendo exportaciones, importaciones y restauraciones automáticas.
 */
class BackupService
{
    protected $backupPath;
    protected $dbPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        $this->dbPath = database_path('database.sqlite');
        
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    /**
     * Lista todas las copias de seguridad disponibles ordenadas por fecha.
     */
    public function listBackups(): array
    {
        $backups = [];
        $files = File::files($this->backupPath);

        foreach ($files as $file) {
            $backups[] = [
                'name' => $file->getFilename(),
                'size' => round($file->getSize() / 1024, 2) . ' KB',
                'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
            ];
        }

        usort($backups, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
        return $backups;
    }

    /**
     * Genera un nuevo punto de restauración manual.
     */
    public function generateBackup(): string
    {
        $filename = 'backup-' . now()->format('Y-m-d-His') . '.sqlite';
        File::copy($this->dbPath, "{$this->backupPath}/{$filename}");
        $this->cleanOldBackups();
        return $filename;
    }

    /**
     * Restaura una base de datos desde un archivo, con backup preventivo.
     */
    public function restoreFromBackup(string $filename): array
    {
        $target = "{$this->backupPath}/{$filename}";

        if (!File::exists($target)) return ['status' => 'error', 'message' => 'Copia no encontrada.'];

        // 1. Crear backup preventivo del estado ACTUAL
        $preventive = 'pre-restore-' . now()->format('Y-m-d-His') . '.sqlite';
        File::copy($this->dbPath, "{$this->backupPath}/{$preventive}");

        // 2. Sobrescribir base de datos principal
        File::copy($target, $this->dbPath);
        
        $this->cleanOldBackups();
        Artisan::call('cache:clear');

        return ['status' => 'success', 'message' => "Base de datos restaurada. Copia preventiva generada: {$preventive}"];
    }

    /**
     * Importa un archivo de backup externo y lo registra.
     */
    public function importExternalBackup(UploadedFile $file): string
    {
        $filename = 'imported-' . now()->format('Y-m-d-His') . '.sqlite';
        $file->move($this->backupPath, $filename);
        $this->cleanOldBackups();
        return $filename;
    }

    /**
     * Elimina un archivo de backup físico.
     */
    public function deleteBackup(string $filename): bool
    {
        $path = "{$this->backupPath}/{$filename}";
        return File::exists($path) ? File::delete($path) : false;
    }

    /**
     * Mantiene solo las 5 copias de seguridad más recientes.
     */
    public function cleanOldBackups(int $limit = 5): void
    {
        $files = File::glob("{$this->backupPath}/*.sqlite");
        if (count($files) > $limit) {
            array_multisort(array_map('filemtime', $files), SORT_ASC, $files);
            File::delete(array_slice($files, 0, count($files) - $limit));
        }
    }
}
