<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\AiAnalystService;

class AiAnalystController extends Controller
{
    protected $aiAnalystService;

    public function __construct(AiAnalystService $aiAnalystService)
    {
        $this->aiAnalystService = $aiAnalystService;
    }

    /**
     * Muestra la página principal del Analista IA con el historial.
     * 
     * @return \Inertia\Response
     */
    public function index()
    {
        $user = Auth::user();
        
        // Verificar si el usuario tiene inversiones activas (cantidad > 0)
        $hasInvestments = \App\Models\Asset::where('user_id', $user->id)
            ->where('quantity', '>', 0)
            ->exists();
        
        // Obtener todos los análisis del usuario ordenados por fecha descendente
        $analyses = $user->aiAnalyses()->orderBy('date', 'desc')->get();
        
        return Inertia::render('AiAnalyst/Index', [
            'user_name' => $user->name,
            'analyses' => $analyses,
            'has_investments' => $hasInvestments,
        ]);
    }

    /**
     * Genera el informe de análisis de la cartera (vía AJAX).
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateReport()
    {
        // Aumentar el tiempo de ejecución para las peticiones de IA
        set_time_limit(120);

        $user = Auth::user();
        
        // Bloqueo de seguridad: No permitir análisis sin inversiones
        $hasInvestments = \App\Models\Asset::where('user_id', $user->id)
            ->where('quantity', '>', 0)
            ->exists();

        if (!$hasInvestments) {
            return response()->json([
                'error' => 'No tienes inversiones activas registradas. El Analista IA requiere datos de tu cartera para generar un informe estratégico.'
            ], 403);
        }

        // Bloqueo por tipo de Membresía (Solo PRO y PREMIUM)
        if ($user->tier !== 'pro' && $user->tier !== 'premium') {
            return response()->json([
                'error' => 'El Análisis Avanzado de Cartera con IA es una función exclusiva de los planes PRO y Premium. Por favor, mejora tu plan para desbloquear esta herramienta estratégica.'
            ], 403);
        }

        $today = now()->format('Y-m-d');

        // Comprobar si ya existe un informe para hoy en la BD
        $existingAnalysis = $user->aiAnalyses()->where('date', $today)->first();
        if ($existingAnalysis) {
            return response()->json(['report' => $existingAnalysis->report]);
        }

        try {
            $report = $this->aiAnalystService->generatePortfolioAnalysis($user);
            
            // Solo guardar si es un informe real (no un mensaje de error del GeminiService)
            if ($report && strpos($report, 'Error') !== 0 && strpos($report, 'Excepción') !== 0) {
                \App\Models\AiAnalysis::create([
                    'user_id' => $user->id,
                    'report' => $report,
                    'date' => $today,
                ]);
            } else {
                $status = (strpos($report, '429') !== false || strpos($report, 'quota') !== false) ? 429 : 400;
                return response()->json(['error' => $report], $status);
            }
            
            return response()->json(['report' => $report]);
        } catch (\Exception $e) {
            Log::error('Error in AiAnalystController: ' . $e->getMessage());
            
            $status = (strpos($e->getMessage(), '429') !== false) ? 429 : 500;
            return response()->json([
                'error' => 'Hubo un problema generando el informe: ' . $e->getMessage()
            ], $status);
        }
    }

}

