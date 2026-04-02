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
        
        // Fetch all analyses for the user ordered by date descending
        $analyses = $user->aiAnalyses()->orderBy('date', 'desc')->get();
        
        return Inertia::render('AiAnalyst/Index', [
            'user_name' => $user->name,
            'analyses' => $analyses,
        ]);
    }

    /**
     * Genera el informe de análisis de la cartera (vía AJAX).
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateReport()
    {
        // Increase execution time for AI requests
        set_time_limit(120);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $today = now()->format('Y-m-d');

        // Check if report already exists for today in DB
        $existingAnalysis = $user->aiAnalyses()->where('date', $today)->first();
        if ($existingAnalysis) {
            return response()->json(['report' => $existingAnalysis->report]);
        }

        try {
            $report = $this->aiAnalystService->generatePortfolioAnalysis($user);
            
            // Only save if it's a real report (not an error message from GeminiService)
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

