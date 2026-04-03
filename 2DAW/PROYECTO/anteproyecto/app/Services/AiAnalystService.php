<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AiAnalystService
{
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Generates a professional financial analysis for the user's portfolio.
     * 
     * @param User $user
     * @return string
     */
    public function generatePortfolioAnalysis(User $user)
    {
        $prompt = $this->preparePrompt($user);
        if (!$prompt) return "Aún no tienes activos registrados en tu cartera. Agrega algunas inversiones para que pueda analizarlas.";

        return $this->geminiService->generateContent($prompt);
    }

    /**
     * Streams the professional financial analysis.
     */
    public function streamPortfolioAnalysis(User $user, callable $onChunk)
    {
        $prompt = $this->preparePrompt($user);
        if (!$prompt) {
            $onChunk("Aún no tienes activos registrados en tu cartera.");
            return;
        }

        $this->geminiService->streamGenerateContent($prompt, $onChunk);
    }

    private function preparePrompt(User $user)
    {
        $assets = Asset::where('user_id', $user->id)
            ->with('marketAsset')
            ->get();

        if ($assets->isEmpty()) {
            return null;
        }

        $totalValue = $assets->sum('current_value');
        $totalInvested = $assets->sum('total_invested');
        $totalProfit = $totalValue - $totalInvested;

        $portfolioData = $assets->map(function ($asset) use ($totalValue) {
            $currentWeight = $totalValue > 0 ? ($asset->current_value / $totalValue) * 100 : 0;
            return [
                'nombre' => $asset->name,
                'ticker' => $asset->ticker,
                'tipo' => $asset->type,
                'cantidad' => $asset->quantity,
                'precio_compra' => $asset->avg_buy_price,
                'precio_actual' => $asset->current_price,
                'valor_actual' => $asset->current_value,
                'ganancia_perdida' => $asset->profit_loss,
                'porcentaje_ganancia' => $asset->profit_loss_percentage,
                'peso_actual_cartera' => round($currentWeight, 2) . '%',
                'sector' => $asset->sector,
                'region' => $asset->region,
            ];
        });

        $recentContributions = $this->getRecentContributions($user);

        return $this->buildAnalysisPrompt($user->name, $portfolioData, $totalInvested, $totalValue, $totalProfit, $recentContributions);
    }

    /**
     * Recopila las aportaciones de los últimos 6 meses.
     */
    private function getRecentContributions(User $user)
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();

        $transactions = Transaction::where('user_id', $user->id)
            ->whereIn('type', ['buy', 'transfer_in', 'gift', 'reward'])
            ->where('date', '>=', $sixMonthsAgo)
            ->with('asset')
            ->orderBy('date', 'desc')
            ->get();

        if ($transactions->isEmpty()) {
            return "No se han registrado aportaciones en los últimos 6 meses.";
        }

        $monthlyData = [];
        foreach ($transactions as $tx) {
            $month = $tx->date->format('F Y');
            $assetName = $tx->asset ? $tx->asset->name : 'General/Desconocido';
            
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = [];
            }
            
            if (!isset($monthlyData[$month][$assetName])) {
                $monthlyData[$month][$assetName] = 0;
            }
            
            $monthlyData[$month][$assetName] += (float) $tx->amount;
        }

        return $monthlyData;
    }

    /**
     * Builds the prompt for the AI model.
     */
    private function buildAnalysisPrompt($userName, $portfolioData, $totalInvested, $totalValue, $totalProfit, $recentContributions)
    {
        $portfolioJson = json_encode($portfolioData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $contributionsJson = json_encode($recentContributions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return "Eres un analista de IA financiero de alto impacto. Tu misión es analizar la cartera de {$userName} con precisión quirúrgica y brevedad extrema.

        DATOS DE CARTERA:
        {$portfolioJson}
        
        RESUMEN GLOBAL:
        - Invertido: {$totalInvested}
        - Valor Actual: {$totalValue}
        - Resultado: {$totalProfit}
        
        HISTORIAL RECIENTE:
        {$contributionsJson}

        ---
        REGLAS DE ESTILO OBLIGATORIAS (CRÍTICAS):
        1. SIN SALUDOS NI CORTESÍAS: Prohibido usar 'Estimado', 'Hola', 'Es un placer', etc. Empieza directamente con la información.
        2. BREVEDAD MÁXIMA: Usa frases cortas, listas de puntos (bullets) y negritas para resaltar datos clave. Si puedes decir algo en 5 palabras, no uses 10.
        3. VALOR DIRECTO: No expliques conceptos básicos. Ve al grano con los datos del usuario.
        4. GANCHO DIARIO: Finaliza siempre con una frase que incite al usuario a volver mañana para ver una métrica específica o un ajuste basado en la evolución del mercado (ej: 'Mañana revisaremos el impacto de la volatilidad en tu meta').

        CONTENIDO DEL INFORME:
        - Diagnóstico vs Benchmarks (S&P500/MSCI World).
        - Riesgo crítico detectado (concentración, volatilidad, etc.).
        - Movimiento sugerido (Rebalanceo específico con %).
        - Guía para próximas aportaciones (DCA Selectivo).
        - Pronóstico flash (Corto, Medio y Largo plazo).

        Idioma: Español. Formato: Markdown limpio.";
    }
}
