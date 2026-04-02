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

        return "Actúa como un analista financiero profesional de nivel senior (CFA Charterholder). 
        Analiza la siguiente cartera de inversiones de {$userName} para evaluar si está bien equilibrada.
        
        Datos de la Cartera (JSON):
        {$portfolioJson}
        
        Resumen Global:
        - Total Invertido: {$totalInvested}
        - Valor Actual: {$totalValue}
        - Ganancia/Pérdida Total: {$totalProfit}
        
        Historial de Aportaciones Recientes (Últimos 6 meses):
        {$contributionsJson}

        Instrucciones Obligatorias para el Informe:
        1. Comparación con Benchmarks: Compara el rendimiento y la composición de esta cartera ÚNICAMENTE con los índices MSCI World y S&P 500. Indica si la cartera está infraponderada o sobreponderada respecto a estos índices en términos de sectores y regiones.
        2. Análisis de Equilibrio: Determina si la cartera está equilibrada según el perfil general de un inversor a largo plazo.
        3. Recomendaciones Concretas de Rebalanceo: Proporciona sugerencias ESPECÍFICAS con porcentajes. 
           - Ejemplo: \"Reducir la exposición a BTC del 15% actual al 10% para disminuir la volatilidad\".
           - Ejemplo: \"Aumentar el peso en Renta Variable Global (MSCI World) del 40% al 55%\".
        4. Ajustes según Aportaciones Mensuales: Analiza el ritmo de aportaciones recientes. Sugiere cómo redirigir el capital de las próximas aportaciones mensuales para alcanzar el equilibrio deseado sin necesidad de vender activos si es posible (enfoque DCA selectivo).
        5. Identifica riesgos de concentración críticos.
        6. Pronóstico:
           - Corto plazo (1-3 meses)
           - Mediano plazo (6-12 meses)
           - Largo plazo (2-5 años)
        
        Tono y Formato:
        - Mantén un tono analítico, profesional y directo.
        - Formateado en Markdown con encabezados (H2, H3), negritas y listas.
        
        Escribe el informe en Español.";
    }
}
