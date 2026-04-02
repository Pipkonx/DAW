<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Prepares the data and prompt for analysis.
     */
    private function preparePrompt(User $user)
    {
        $assets = Asset::where('user_id', $user->id)
            ->with('marketAsset')
            ->get();

        if ($assets->isEmpty()) {
            return null;
        }

        $portfolioData = $assets->map(function ($asset) {
            return [
                'nombre' => $asset->name,
                'ticker' => $asset->ticker,
                'tipo' => $asset->type,
                'cantidad' => $asset->quantity,
                'precio_compra' => $asset->avg_buy_price,
                'precio_actual' => $asset->current_price,
                'valor_actual' => $asset->current_value,
                'ganancia_perdida' => $asset->profit_loss,
                'porcentaje' => $asset->profit_loss_percentage,
                'sector' => $asset->sector,
                'region' => $asset->region,
            ];
        });

        $totalInvested = $assets->sum('total_invested');
        $totalValue = $assets->sum('current_value');
        $totalProfit = $totalValue - $totalInvested;

        return $this->buildAnalysisPrompt($user->name, $portfolioData, $totalInvested, $totalValue, $totalProfit);
    }

    /**
     * Builds the prompt for the AI model.
     */
    private function buildAnalysisPrompt($userName, $portfolioData, $totalInvested, $totalValue, $totalProfit)
    {
        $portfolioJson = json_encode($portfolioData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return "Actúa como un analista financiero profesional de nivel senior (CFA Charterholder). 
        Analiza la siguiente cartera de inversiones del usuario {$userName}.
        
        Datos de la Cartera (JSON):
        {$portfolioJson}
        
        Resumen Global:
        - Total Invertido: {$totalInvested}
        - Valor Actual: {$totalValue}
        - Ganancia/Pérdida Total: {$totalProfit}
        
        Instrucciones para el Informe:
        1. Comienza con un saludo profesional y una visión general del estado actual de la cartera.
        2. Analiza las posiciones individuales: cuáles están funcionando bien, cuáles no, y por qué (basándote en tendencias de mercado generales o la naturaleza de los activos).
        3. Identifica riesgos de concentración (por sector, tipo de activo o región) si los hay.
        4. Proporciona un pronóstico detallado:
           - Corto plazo (1-3 meses)
           - Mediano plazo (6-12 meses)
           - Largo plazo (2-5 años)
        5. Sugiere ajustes estratégicos (ej. rebalanceo, diversificación) de forma prudente.
        6. Mantén un tono profesional, analítico, educativo y alentador, pero advirtiendo siempre sobre los riesgos del mercado.
        7. El informe debe estar formateado en Markdown limpio y profesional, usando encabezados, negritas y listas.
        
        Escribe el informe en Español.";
    }
}
