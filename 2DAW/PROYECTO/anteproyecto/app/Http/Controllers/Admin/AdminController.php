<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Report;
use App\Models\Transaction;
use App\Models\Ticket;
use App\Services\Admin\AdminService;
use App\Services\Admin\BackupService;
use App\Services\User\UserService;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * AdminController - Orquestador del Panel de Control de Pipkonx.
 * 
 * Gestiona la visibilidad global del sistema, la administración de usuarios,
 * planes de suscripción y la integridad de los datos mediante backups.
 */
class AdminController extends Controller
{
    protected $adminService;
    protected $backupService;
    protected $userService;

    public function __construct(AdminService $adminService, BackupService $backupService, UserService $userService)
    {
        $this->adminService = $adminService;
        $this->backupService = $backupService;
        $this->userService = $userService;
    }

    /**
     * Muestra el resumen ejecutivo para administradores.
     */
    public function index(ApiService $apiService)
    {
        return Inertia::render('Admin/Dashboard', [
            'backups' => $this->backupService->listBackups(),
            'users' => User::orderBy('created_at', 'desc')->get(),
            'stats' => array_merge(
                $this->adminService->getUserStats(),
                $this->adminService->getGlobalStats(),
                $this->adminService->getSystemHealth()
            ),
            'api_consumption' => $apiService->getConsumptionData(),
            'global_activity' => Transaction::with(['user', 'asset'])->latest('date')->limit(5)->get()->map(fn($tx) => [
                'id' => $tx->id, 'user' => $tx->user->name, 'type' => $tx->type,
                'amount' => (float) $tx->amount, 'asset' => $tx->asset?->name ?? 'Efectivo',
                'date' => $tx->date->format('d/m/Y H:i')
            ]),
            'reports' => Report::with('user', 'reportable')->latest()->take(50)->get()->map(fn($r) => [
                'id' => $r->id, 'user_name' => $r->user->name, 'reason' => $r->reason,
                'type' => class_basename($r->reportable_type), 'ref_id' => $r->reportable_id,
                'date' => $r->created_at->diffForHumans()
            ]),
            'support_tickets_count' => Ticket::where('status', 'open')->count(),
        ]);
    }

    /**
     * Alternar privilegios administrativos.
     */
    public function toggleAdmin(User $user)
    {
        if (!$this->userService->toggleAdmin(Auth::user(), $user)) {
            return back()->with('error', 'No puedes alterar tus propios permisos.');
        }
        return back()->with('success', 'Permisos de usuario actualizados.');
    }

    /**
     * Eliminar cuenta de usuario permanentemente.
     */
    public function deleteUser(User $user)
    {
        if ($user->id === Auth::id()) return back()->with('error', 'No puedes eliminar tu propia cuenta desde aquí.');
        $user->delete();
        return back()->with('success', 'Usuario eliminado satisfactoriamente.');
    }

    /**
     * Conceder planes de suscripción manualmente.
     */
    public function updateSubscription(Request $request, User $user)
    {
        $validated = $request->validate([
            'tier' => 'required|in:none,basic,pro,premium',
            'days' => 'nullable|integer|min:1'
        ]);

        $this->userService->updateSubscription($user, $validated['tier'], $validated['days'] ?? 30);
        return back()->with('success', 'Suscripción actualizada correctamente.');
    }

    /**
     * Gestión de Copias de Seguridad.
     */
    public function generateBackup()
    {
        $filename = $this->backupService->generateBackup();
        return back()->with('success', "Copia de seguridad {$filename} generada.");
    }

    public function restoreBackup($filename)
    {
        $result = $this->backupService->restoreFromBackup($filename);
        return back()->with($result['status'], $result['message']);
    }

    public function deleteBackup($filename)
    {
        if ($this->backupService->deleteBackup($filename)) return back()->with('success', 'Archivo eliminado.');
        return back()->with('error', 'No se pudo eliminar el archivo.');
    }

    public function downloadBackup($filename)
    {
        return response()->download(storage_path("app/backups/{$filename}"));
    }

    /**
     * Importar un archivo de copia de seguridad externo.
     */
    public function importBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file',
        ]);

        try {
            $filename = $this->backupService->importExternalBackup($request->file('backup_file'));
            return back()->with('success', "Copia externa {$filename} importada correctamente.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }

    /**
     * Subir y restaurar una copia de seguridad directamente.
     */
    public function restoreDirect(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file',
        ]);

        try {
            // 1. Importar temporalmente
            $filename = $this->backupService->importExternalBackup($request->file('backup_file'));
            
            // 2. Restaurar inmediatamente
            $result = $this->backupService->restoreFromBackup($filename);
            
            return back()->with($result['status'], $result['message']);
        } catch (\Exception $e) {
            return back()->with('error', 'Error en restauración directa: ' . $e->getMessage());
        }
    }

    /**
     * Optimiza y limpia el entorno del sistema.
     */
    public function clearCache()
    {
        Artisan::call('cache:clear');
        return back()->with('success', 'Caché de la aplicación purgada.');
    }

    public function optimizeDb()
    {
        try {
            \DB::statement('VACUUM'); // Optimización nativa SQLite
            return back()->with('success', 'Base de datos compactada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error en optimización: ' . $e->getMessage());
        }
    }
}
