<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Asset;
use App\Models\Transaction;
use App\Services\ApiService;

class AdminController extends Controller
{
    /**
     * Muestra el panel de administración con telemetría extendida, backups y gestión.
     */
    public function index(ApiService $apiService)
    {
        $backupDir = storage_path('app/backups');
        $backups = [];

        if (File::exists($backupDir)) {
            $files = File::files($backupDir);
            foreach ($files as $file) {
                $backups[] = [
                    'name' => $file->getFilename(),
                    'size' => round($file->getSize() / 1024, 2) . ' KB',
                    'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                ];
            }
        }

        usort($backups, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return Inertia::render('Admin/Dashboard', [
            'backups' => $backups,
            'users' => User::orderBy('created_at', 'desc')->get(),
            'stats' => [
                'users' => User::count(),
                'assets' => Asset::count(),
                'transactions' => Transaction::count(),
                'db_size' => round(File::size(database_path('database.sqlite')) / 1024 / 1024, 2) . ' MB',
                'cache_enabled' => config('cache.default') !== 'array'
            ],
            'api_health' => $this->checkApiHealth(),
            'api_consumption' => $apiService->getConsumptionData(),
            'global_activity' => Transaction::with(['user', 'asset'])
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(fn($tx) => [
                    'id' => $tx->id,
                    'user' => $tx->user->name,
                    'type' => $tx->type,
                    'amount' => (float) $tx->amount,
                    'asset' => $tx->asset?->name ?? 'Efectivo/General',
                    'date' => $tx->date->format('d/m/Y H:i'),
                ])
        ]);
    }

    /**
     * Alterna el rol de administrador de un usuario.
     */
    public function toggleAdmin(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes quitarte los permisos a ti mismo.');
        }

        $user->update(['is_admin' => !$user->is_admin]);
        return back()->with('success', 'Rol de usuario actualizado.');
    }

    /**
     * Elimina un usuario definitivamente.
     */
    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        $user->delete();
        return back()->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * Restaura una copia de seguridad específica.
     */
    public function restoreBackup($filename)
    {
        $backupPath = storage_path('app/backups/' . $filename);
        $dbPath = database_path('database.sqlite');

        if (!File::exists($backupPath)) {
            return back()->with('error', 'Archivo de copia no encontrado.');
        }

        try {
            // SEGURIDAD: Realizar backup preventivo del estado ACTUAL antes de sobrescribir
            $preventiveName = 'pre-restore-' . now()->format('Y-m-d-His') . '.sqlite';
            File::copy($dbPath, storage_path('app/backups/' . $preventiveName));

            // Aplicar restauración
            File::copy($backupPath, $dbPath);

            return back()->with('success', 'Base de datos restaurada con éxito. Se ha creado una copia preventiva: ' . $preventiveName);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al restaurar: ' . $e->getMessage());
        }
    }

    /**
     * Optimiza la base de datos SQLite (VACUUM).
     */
    public function optimizeDb()
    {
        try {
            \DB::statement('VACUUM');
            return back()->with('success', 'Base de datos optimizada (VACUUM ejecutado).');
        } catch (\Exception $e) {
            return back()->with('error', 'Error en optimización: ' . $e->getMessage());
        }
    }

    /**
     * Limpia la caché de la aplicación.
     */
    public function clearCache()
    {
        \Artisan::call('cache:clear');
        return back()->with('success', 'Caché de la aplicación limpiada.');
    }

    /**
     * Verifica la conectividad con las APIs externas.
     */
    private function checkApiHealth()
    {
        $apis = [
            'FMP' => 'https://financialmodelingprep.com/api/v3/actives?apikey=' . (config('services.fmp.key') ?? env('FMP_API_KEY')),
            'CoinGecko' => 'https://api.coingecko.com/api/v3/ping'
        ];

        $results = [];
        foreach ($apis as $name => $url) {
            try {
                $response = \Http::timeout(3)->get($url);
                $results[$name] = $response->successful();
            } catch (\Exception $e) {
                $results[$name] = false;
            }
        }
        return $results;
    }

    /**
     * Genera un nuevo backup de la base de datos SQLite.
     */
    public function generateBackup()
    {
        $databasePath = database_path('database.sqlite');
        if (!File::exists($databasePath)) {
            return back()->with('error', 'Base de datos no encontrada.');
        }

        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = 'backup-' . now()->format('Y-m-d-His') . '.sqlite';
        File::copy($databasePath, $backupDir . '/' . $filename);

        return back()->with('success', 'Copia de seguridad ' . $filename . ' generada con éxito.');
    }

    /**
     * Descarga un archivo de backup.
     */
    public function downloadBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        if (!File::exists($path)) {
            abort(404);
        }
        return response()->download($path);
    }

    /**
     * Elimina un archivo de backup.
     */
    public function deleteBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        if (File::exists($path)) {
            File::delete($path);
            return back()->with('success', 'Copia de seguridad eliminada.');
        }
        return back()->with('error', 'Archivo no encontrado.');
    }

    /**
     * Importa una copia de seguridad externa (.sqlite).
     */
    public function importBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file',
        ]);

        $file = $request->file('backup_file');

        if ($file->getClientOriginalExtension() !== 'sqlite') {
            return back()->with('error', 'Solo se permiten archivos .sqlite');
        }

        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $filename = 'imported-' . now()->format('Y-m-d-His') . '.sqlite';
        $file->move($backupDir, $filename);

        return back()->with('success', 'Copia de seguridad importada con éxito: ' . $filename);
    }

    /**
     * Importa y aplica una copia de seguridad inmediatamente (Emergencia).
     */
    public function restoreDirect(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file',
        ]);

        $file = $request->file('backup_file');

        if ($file->getClientOriginalExtension() !== 'sqlite') {
            return back()->with('error', 'Solo se permiten archivos .sqlite');
        }

        $databasePath = database_path('database.sqlite');
        $backupDir = storage_path('app/backups');

        // 1. Realizar backup preventivo del estado actual
        if (File::exists($databasePath)) {
            $preventiveName = 'pre-direct-restore-' . now()->format('Y-m-d-His') . '.sqlite';
            File::copy($databasePath, $backupDir . '/' . $preventiveName);
        }

        // 2. Sobrescribir base de datos actual
        File::copy($file->getRealPath(), $databasePath);

        // 3. Limpiar caché para evitar inconsistencias
        Artisan::call('cache:clear');

        return back()->with('success', 'Base de datos restaurada directamente con éxito. El sistema ha sido actualizado.');
    }

    /**
     * Obtiene las últimas líneas del log del sistema.
     */
    public function getSystemLogs()
    {
        $logPath = storage_path('logs/laravel.log');

        if (!File::exists($logPath)) {
            return response()->json(['logs' => 'No se encontró el archivo de log.']);
        }

        // Leer las últimas 50 líneas
        $file = escapeshellarg($logPath);
        $lineCount = 50;

        // Usar comando tail si estamos en linux, o leer de forma nativa en PHP para compatibilidad
        $logs = [];
        $handle = fopen($logPath, "r");
        if ($handle) {
            $lineCountSeen = 0;
            $pos = -2;
            $buffer = "";

            while ($lineCountSeen < $lineCount) {
                if (fseek($handle, $pos, SEEK_END) == -1)
                    break;
                $char = fgetc($handle);
                if ($char == "\n") {
                    $lineCountSeen++;
                }
                $pos--;
            }
            $logs = fread($handle, abs($pos));
            fclose($handle);
        }

        return response()->json(['logs' => $logs ?: 'Log vacío o no legible.']);
    }
}
