<?php

namespace App\Services\Analysis;

use App\Models\Transaction;
use App\Models\Asset;
use Carbon\Carbon;
use App\Services\Analysis\ValuationEngine;
use Illuminate\Support\Facades\DB;

/**
 * PerformanceService - Analista cuantitativo de Pipkonx.
 * 
 * Centraliza los informes de rendimiento, distribución de activos y 
 * analítica de rentabilidad histórica (ROI, TWR, IRR).
 */
class PerformanceService
{
    protected $valuationEngine;

    public function __construct(ValuationEngine $valuationEngine)
    {
        $this->valuationEngine = $valuationEngine;
    }

    /**
     * Distribución de activos (Asset Allocation) agrupada dinámicamente.
     */
    public function getAllocation($assets, $field)
    {
        return $assets->groupBy($field)
            ->map(fn ($group, $key) => [
                'label' => $key ?: 'Otros',
                'value' => (float) $group->sum('current_value'),
                'color' => '#' . substr(md5((string) $key), 0, 6)
            ])->values();
    }

    /**
     * Generación de series temporales para gráficos de rendimiento.
     */
    public function getChartData($userId, $assetIds, $timeframe, $currentValue = null)
    {
        $startDate = $this->getStartDate($userId, $assetIds, $timeframe);
        $endDate = now();

        $dataPoints = $this->valuationEngine->getDailyHistoricalValuation($userId, $assetIds, $startDate, $endDate);
        
        // Calcular plusvalía del periodo (P/L)
        $first = $dataPoints[0] ?? null;
        $last = end($dataPoints) ?? null;
        $plValue = 0; $plPercent = 0;

        if ($first && $last) {
            $plValue = ($last['value'] - $last['cost']) - ($first['value'] - $first['cost']);
            $denom = $first['value'] > 0 ? $first['value'] : ($first['cost'] > 0 ? $first['cost'] : 1);
            $plPercent = ($plValue / $denom) * 100;
        }

        return [
            'labels' => array_column($dataPoints, 'date'),
            'data' => array_column($dataPoints, 'value'),
            'invested' => array_column($dataPoints, 'cost'),
            'period_pl_value' => (float) $plValue,
            'period_pl_percent' => (float) $plPercent,
        ];
    }

    /**
     * Desglose detallado de métricas (dividendos, comisiones, impuestos, ROI real).
     */
    public function getDetailedBreakdown($userId, $assetIds, $year = null, $preloadedTxs = null, $preloadedAssets = null)
    {
        $allTxs = $preloadedTxs ?? Transaction::where('user_id', $userId)
            ->whereIn('asset_id', $assetIds)
            ->when($year, fn($q) => $q->whereYear('date', '<=', $year))
            ->orderBy('date', 'asc')
            ->get();

        $yearTxs = $year 
            ? $allTxs->filter(fn($t) => Carbon::parse($t->date)->year == $year) 
            : $allTxs;
            
        $assets = $preloadedAssets ?? Asset::whereIn('id', $assetIds)->get()->keyBy('id');

        // Cálculo resumido de gastos e ingresos
        $dividendos = (float) $yearTxs->where('type', 'dividend')->sum('amount');
        $comisiones = (float) ($yearTxs->sum('fees') + $yearTxs->sum('exchange_fees'));
        $impuestos = (float) $yearTxs->sum('tax');
        $gananciaRealizada = (float) $yearTxs->where('type', 'sell')->sum(fn($t) => $t->amount - ($t->quantity * ($assets->get($t->asset_id)->avg_buy_price ?? 0)));

        $state = [];
        if ($year) {
            foreach ($allTxs->filter(fn($t) => Carbon::parse($t->date)->year < $year) as $tx) {
                $this->valuationEngine->updateAssetState($state, $tx);
            }
            $startVal = $this->valuationEngine->calculateInstantValuation($state, $assets);
            $valueStart = $startVal['value']; $costStart = $startVal['cost'];
            $capitalYear = (float) $yearTxs->where('type', 'buy')->sum('amount');
            $capitalInvertido = $costStart + $capitalYear;
        } else {
            $capitalInvertido = (float) $allTxs->where('type', 'buy')->sum('amount');
            $valueStart = 0; $costStart = 0;
        }

        foreach ($yearTxs as $tx) {
            $this->valuationEngine->updateAssetState($state, $tx);
        }
        $endVal = $this->valuationEngine->calculateInstantValuation($state, $assets);
        $valueEnd = $endVal['value']; $costEnd = $endVal['cost'];

        // Algoritmo de rentabilidad total
        if ($year) {
            $retornoTotal = ($valueEnd - $costEnd) - ($valueStart - $costStart);
            $rendimientoPrecio = $retornoTotal - $dividendos - $gananciaRealizada + $comisiones + $impuestos;
        } else {
            $rendimientoPrecio = $valueEnd - ($capitalInvertido - $comisiones);
            $retornoTotal = $rendimientoPrecio + $dividendos + $gananciaRealizada - $comisiones - $impuestos;
        }

        return [
            'capital_invertido' => (float)$capitalInvertido,
            'price_gain' => (float)$rendimientoPrecio,
            'dividends' => (float)$dividendos,
            'realized_gain' => (float)$gananciaRealizada,
            'fees' => (float)$comisiones,
            'taxes' => (float)$impuestos,
            'total_roi' => (float)$retornoTotal,
            'total_roi_percent' => $capitalInvertido > 0 ? ($retornoTotal / $capitalInvertido) * 100 : 0,
        ];
    }

    /**
     * Mapa de calor y analítica anualizada.
     */
    public function getAnnualPerformance($userId, $assetIds)
    {
        $txQuery = Transaction::where('user_id', $userId)->whereIn('asset_id', $assetIds);
        $allTxs = $txQuery->orderBy('date', 'asc')->get();
        $assets = Asset::whereIn('id', $assetIds)->get()->keyBy('id');

        $years = DB::table('transactions')
            ->where('user_id', $userId)
            ->whereIn('asset_id', is_array($assetIds) ? $assetIds : $assetIds->toArray())
            ->selectRaw('DISTINCT YEAR(date) as year')
            ->pluck('year')
            ->sort();

        if ($years->isEmpty()) $years = [now()->year];

        $labels = []; $data = [];
        foreach ($years as $year) {
            // Pasamos las colecciones ya cargadas para evitar N+1 y saturación de memoria
            $breakdown = $this->getDetailedBreakdown($userId, $assetIds, $year, $allTxs, $assets);
            $labels[] = $year;
            $data[] = $breakdown['total_roi'];
        }
        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Genera datos para el mapa de calor de rendimiento diario.
     */
    public function getHeatmapData($userId, $assetIds)
    {
        $txs = Transaction::where('user_id', $userId)
            ->whereIn('asset_id', $assetIds)
            ->selectRaw('YEAR(date) as year, MONTH(date) as month, 
                         SUM(CASE WHEN type IN ("income", "dividend", "sell", "reward", "gift") THEN amount 
                                  WHEN type IN ("expense", "buy") THEN -amount 
                                  ELSE 0 END) as value')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'asc')
            ->get();

        $heatmap = [];
        $years = $txs->pluck('year')->unique();

        foreach ($years as $year) {
            $monthsData = array_fill(0, 12, 0);
            $yearTxs = $txs->where('year', $year);
            foreach ($yearTxs as $tx) {
                $monthsData[$tx->month - 1] = (float)$tx->value;
            }
            $heatmap[] = [
                'year' => $year,
                'months' => $monthsData
            ];
        }

        return $heatmap;
    }

    /**
     * Recupera el rendimiento mensual detallado para un año específico.
     */
    public function getMonthlyPerformance($userId, $assetIds, $year)
    {
        $allYearTxs = Transaction::where('user_id', $userId)
            ->whereIn('asset_id', $assetIds)
            ->whereYear('date', $year)
            ->get();

        $monthly = [];
        for ($m = 1; $m <= 12; $m++) {
            $startDate = Carbon::create($year, $m, 1)->startOfMonth();
            if ($startDate->gt(now())) break;

            $txs = $allYearTxs->filter(fn($t) => Carbon::parse($t->date)->month == $m);

            $monthly[] = [
                'month' => $startDate->translatedFormat('F'),
                'roi' => (float)$txs->sum('amount'),
                'count' => $txs->count()
            ];
        }
        return $monthly;
    }

    /**
     * Determina el inicio del análisis temporal.
     */
    private function getStartDate($userId, $assetIds, $timeframe)
    {
        if ($timeframe === 'MAX') {
            $query = Transaction::where('user_id', $userId);
            if (!empty($assetIds)) {
                $query->whereIn('asset_id', (array)$assetIds);
            }
            $firstTx = $query->orderBy('date', 'asc')->first();
            return $firstTx ? Carbon::parse($firstTx->date) : now()->subMonth();
        }

        return match($timeframe) {
            '1D' => now()->subDay(), '1W' => now()->subWeek(), '1M' => now()->subMonth(),
            '3M' => now()->subMonths(3), '1Y' => now()->subYear(), 'YTD' => now()->startOfYear(),
            default => now()->subMonth(),
        };
    }
}
