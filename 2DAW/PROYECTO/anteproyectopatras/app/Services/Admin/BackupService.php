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
    protected $mysqlPath;
    protected $mysqldumpPath;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        $this->mysqlPath = 'C:\xampp\mysql\bin\mysql.exe';
        $this->mysqldumpPath = 'C:\xampp\mysql\bin\mysqldump.exe';
        
        if (!File::exists($this->backupPath)) {
            File::makeDirectory($this->backupPath, 0755, true);
        }
    }

    /**
     * Lista todas las copias de seguridad (.sql) disponibles ordenadas por fecha.
     */
    public function listBackups(): array
    {
        $backups = [];
        $files = File::files($this->backupPath);

        foreach ($files as $file) {
            if ($file->getExtension() === 'sql') {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => round($file->getSize() / 1024, 2) . ' KB',
                    'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
            }
        }

        usort($backups, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
        return $backups;
    }

    /**
     * Genera un nuevo volcado SQL (MySQLdump).
     */
    public function generateBackup(): string
    {
        $filename = 'backup-' . now()->format('Y-m-d-His') . '.sql';
        $path = "{$this->backupPath}/{$filename}";
        
        $command = $this->buildMysqlCommand($this->mysqldumpPath, "> " . escapeshellarg($path));
        
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception("Error al generar el volcado MySQL: " . implode("\n", $output));
        }

        $this->cleanOldBackups();
        return $filename;
    }

    /**
     * Restaura la base de datos MySQL desde un archivo .sql.
     */
    public function restoreFromBackup(string $filename): array
    {
        $target = "{$this->backupPath}/{$filename}";

        if (!File::exists($target)) return ['status' => 'error', 'message' => 'Copia no encontrada.'];

        try {
            // 1. Crear backup preventivo del estado ACTUAL
            $preventive = 'pre-restore-' . now()->format('Y-m-d-His') . '.sql';
            $this->generateBackupWithName($preventive);

            // 2. Ejecutar restauración vía cliente MySQL
            $command = $this->buildMysqlCommand($this->mysqlPath, "< " . escapeshellarg($target));
            
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                throw new \Exception("Error al restaurar MySQL: " . implode("\n", $output));
            }
            
            // 3. Limpiar cachés
            $this->cleanOldBackups();
            Artisan::call('cache:clear');
            Artisan::call('view:clear');

            return [
                'status' => 'success', 
                'message' => "Base de datos MySQL restaurada con éxito desde {$filename}. Se generó copia preventiva: {$preventive}"
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error', 
                'message' => 'Fallo crítico en la restauración MySQL: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Genera un backup con un nombre específico (para restauraciones preventivas).
     */
    private function generateBackupWithName(string $filename): void
    {
        $path = "{$this->backupPath}/{$filename}";
        $command = $this->buildMysqlCommand($this->mysqldumpPath, "> " . escapeshellarg($path));
        exec($command);
    }

    /**
     * Construye el comando MySQL con credenciales de .env.
     */
    private function buildMysqlCommand(string $binary, string $redirection): string
    {
        $db = config('database.connections.mysql.database');
        $user = config('database.connections.mysql.username');
        $pass = config('database.connections.mysql.password');
        
        $cmd = escapeshellarg($binary) . " -u " . escapeshellarg($user);
        if ($pass) {
            $cmd .= " -p" . escapeshellarg($pass);
        }
        $cmd .= " " . escapeshellarg($db) . " {$redirection} 2>&1";
        
        return $cmd;
    }

    /**
     * Importa un archivo .sql externo y lo registra.
     */
    public function importExternalBackup(UploadedFile $file): string
    {
        $filename = 'imported-' . now()->format('Y-m-d-His') . '.sql';
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
        $files = File::glob("{$this->backupPath}/*.sql");
        if (count($files) > $limit) {
            array_multisort(array_map('filemtime', $files), SORT_ASC, $files);
            File::delete(array_slice($files, 0, count($files) - $limit));
        }
    }
}
