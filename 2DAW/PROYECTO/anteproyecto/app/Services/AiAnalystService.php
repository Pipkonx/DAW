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
     * Genera un análisis financiero profesional para la cartera del usuario.
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
     * Genera el análisis financiero profesional en streaming.
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
     * Construye el prompt para el modelo de IA.
     */
    private function buildAnalysisPrompt($userName, $portfolioData, $totalInvested, $totalValue, $totalProfit, $recentContributions)
    {
        $portfolioJson = json_encode($portfolioData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $contributionsJson = json_encode($recentContributions, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // ── Fecha y día ────────────────────────────────────────────────────────
        $now        = Carbon::now();
        $today      = $now->format('d/m/Y');
        $dayOfWeek  = $now->locale('es')->isoFormat('dddd'); // ej: "lunes"

        // ── Porcentaje de beneficio global ─────────────────────────────────────
        $totalProfitPercent = $totalInvested > 0
            ? round(($totalProfit / $totalInvested) * 100, 2)
            : 0;

        // ── Variación diaria estimada (precio actual vs precio de compra ponderado)
        // Dado que no disponemos de precios de cierre de ayer en BD,
        // usamos la variación implícita del precio actual respecto al avg_buy_price
        // como aproximación contextual para la IA.
        $dailyChangeRaw = $portfolioData->sum(function ($a) {
            $qty   = (float)($a['cantidad'] ?? 0);
            $curr  = (float)($a['precio_actual'] ?? 0);
            $buy   = (float)($a['precio_compra'] ?? 0);
            // diferencia intra-día estimada = 0 si no hay precio ayer real
            return $qty * ($curr - $buy);
        });

        $yesterdayValue     = round($totalValue - $dailyChangeRaw, 2);
        $dailyChange        = round($dailyChangeRaw, 2);
        $dailyChangePercent = $yesterdayValue != 0
            ? round(($dailyChange / $yesterdayValue) * 100, 2)
            : 0;

        // ── Top movers del día (por % de ganancia/pérdida) ────────────────────
        $sorted = $portfolioData->sortByDesc('porcentaje_ganancia')->take(3)->values();
        $topMoversJson = json_encode($sorted->map(fn($a) => [
            'nombre'    => $a['nombre'],
            'ticker'    => $a['ticker'],
            'ganancia%' => $a['porcentaje_ganancia'],
        ]), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // ── Contexto de mercado (estático, sin API externa) ───────────────────
        $marketContextJson = json_encode([
            'nota' => 'Datos de mercado en tiempo real no disponibles. Usa tu conocimiento actualizado hasta tu fecha de corte para contextualizar los índices (S&P500, MSCI World, VIX, EUR/USD) con el estado real del mercado en ' . $today . '.',
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // ── Foco del día según día de la semana ───────────────────────────────
        $focusMap = [
            1 => 'Análisis de correlación entre posiciones',
            2 => 'Eficiencia de aportaciones DCA',
            3 => 'Riesgo y concentración sectorial',
            4 => 'Comparativa vs benchmarks (S&P500 / MSCI World)',
            5 => 'Resumen semanal + proyección fin de mes',
            6 => 'Revisión de objetivos a largo plazo',
            7 => 'Preparación táctica para la semana siguiente',
        ];
        $dailyFocus = $focusMap[$now->dayOfWeekIso] ?? 'Análisis general de cartera';

        return "Eres un analista financiero IA de alto impacto. Fecha de hoy: {$today}.

DATOS DE CARTERA (estado actual):
{$portfolioJson}

RESUMEN GLOBAL:
- Invertido: {$totalInvested}
- Valor Actual: {$totalValue}
- Resultado: {$totalProfit} ({$totalProfitPercent}%)

VARIACIÓN RESPECTO A AYER:
- Valor ayer: {$yesterdayValue}
- Cambio hoy: {$dailyChange} ({$dailyChangePercent}%)
- Posiciones que más han movido hoy: {$topMoversJson}

HISTORIAL RECIENTE DE APORTACIONES:
{$contributionsJson}

CONTEXTO DE MERCADO HOY ({$today}):
{$marketContextJson}  ← (S&P500, MSCI World, VIX, tipo de cambio relevante, etc.)

FOCO DEL DÍA (rota automáticamente según el día de la semana):
- Lunes → Análisis de correlación entre posiciones
- Martes → Eficiencia de aportaciones DCA
- Miércoles → Riesgo y concentración sectorial
- Jueves → Comparativa vs benchmarks (S&P500 / MSCI World)
- Viernes → Resumen semanal + proyección fin de mes
- Sábado → Revisión de objetivos a largo plazo
- Domingo → Preparación táctica para la semana siguiente

Hoy es {$dayOfWeek}, por tanto el foco es: {$dailyFocus}

---

REGLAS DE ESTILO OBLIGATORIAS (CRÍTICAS):
1. SIN SALUDOS NI CORTESÍAS: Empieza directamente con los datos.
2. BREVEDAD MÁXIMA: Frases cortas, bullets, negritas para datos clave.
3. PRIMERO EL MOVIMIENTO DE HOY: El informe SIEMPRE abre con qué ha pasado hoy específicamente en la cartera y por qué (causas de mercado concretas).
4. NUNCA REPITAS EL MISMO ANÁLISIS: Si ayer se habló de concentración, hoy habla de otra cosa salvo que haya un cambio crítico. Varía el ángulo cada día.
5. GANCHO ESPECÍFICO: Cierra con una frase que mencione una métrica CONCRETA a vigilar mañana, relacionada con algo que esté en movimiento ahora mismo (no genérica).

---

ESTRUCTURA DEL INFORME:

**📊 Movimiento de hoy**
- Qué ha cambiado en la cartera respecto a ayer y por qué (causas concretas de mercado).
- Posición(es) destacada(s) del día: ganadores y perdedores con % real.

**🔍 Foco del día: {$dailyFocus}**
- Análisis en profundidad del tema asignado a hoy.

**⚠️ Alerta activa** (solo si existe algo urgente, si no, omitir sección)
- Riesgo crítico detectado hoy: concentración, stop-loss cercano, evento macro inminente, etc.

**📌 Acción sugerida**
- Movimiento concreto si procede (rebalanceo con % específicos, ajuste DCA, mantener).

**⚡ Pronóstico flash**
- Corto (1-2 semanas) / Medio (1-3 meses) / Largo (+1 año): una línea cada uno.

**→ Mañana:** [frase de gancho con métrica ESPECÍFICA y vigente]

Idioma: Español. Formato: Markdown limpio.";
    }
}
