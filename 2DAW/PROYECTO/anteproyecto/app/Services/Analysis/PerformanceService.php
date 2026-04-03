<?php

namespace App\Services\Analysis;

use App\Models\Transaction;
use App\Models\Asset;
use App\Services\Analysis\Concerns\ManagesAssetState;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * Servicio central para cálculos de rendimiento y analítica financiera.
 */
class PerformanceService
{
    use ManagesAssetState;

    /**
     * Calcula la distribución de activos (Asset Allocation) agrupada por un campo específico.
     */
    public function getAllocation($assets, $field)
    {
        return $assets->groupBy($field)
            ->map(function ($group, $key) {
                return [
                    'label' => $key ?: 'Otros',
                    'value' => (float) $group->sum('current_value'),
                    'color' => '#' . substr(md5((string) $key), 0, 6)
                ];
            })->values();
    }

    /**
     * Genera los datos para el gráfico de rendimiento y calcula la plusvalía del periodo seleccionado.
     */
    public function getChartData($userId, $assetIds, $timeframe, $currentTotalValue)
    {
        $endDate = Carbon::now();
        $startDate = $this->getStartDate($userId, $assetIds, $timeframe);

        $allTransactions = Transaction::where('user_id', $userId)
            ->whereIn('asset_id', $assetIds)
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
            
        $transactionsByDate = $allTransactions->groupBy(fn($t) => substr($t->date, 0, 10));
        $period = CarbonPeriod::create($startDate, '1 day', $endDate);
        
        $assetsState = [];
        $assets = Asset::whereIn('id', $assetIds)->get()->keyBy('id');
        
        // 1. Establecer el punto base anterior a la fecha de inicio
        foreach ($allTransactions as $tx) {
            $txDate = Carbon::parse($tx->date);
            if ($txDate->lt($startDate)) {
                $this->updateAssetState($assetsState, $tx);
            }
        }
        
        // 2. Iteración diaria para construir el histórico
        $dataPoints = [];
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            
            if (isset($transactionsByDate[$dateStr])) {
                foreach ($transactionsByDate[$dateStr] as $tx) {
                    $this->updateAssetState($assetsState, $tx);
                }
            }
            
            $valuation = $this->calculateCurrentValuation($assetsState, $assets);

            $dataPoints[] = [
                'x' => $dateStr,
                'y' => round($valuation['value'], 2),
                'invested' => round($valuation['cost'], 2)
            ];
        }

        $performance = $this->calculatePeriodPerformance($dataPoints);

        return [
            'labels' => array_column($dataPoints, 'x'),
            'data' => array_column($dataPoints, 'y'),
            'invested' => array_column($dataPoints, 'invested'),
            'period_pl_value' => $performance['value'],
            'period_pl_percent' => $performance['percent'],
        ];
    }

    /**
     * Determina la fecha de inicio según el selector de rango temporal.
     */
    private function getStartDate($userId, $assetIds, $timeframe)
    {
        if ($timeframe === 'MAX') {
            $firstTx = Transaction::where('user_id', $userId)
                ->whereIn('asset_id', $assetIds)
                ->orderBy('date', 'asc')
                ->first();
            
            return $firstTx ? Carbon::parse($firstTx->date) : Carbon::now()->subMonth();
        }

        return match($timeframe) {
            '1D' => Carbon::now()->subDay(),
            '1W' => Carbon::now()->subWeek(),
            '1M' => Carbon::now()->subMonth(),
            '3M' => Carbon::now()->subMonths(3),
            '1Y' => Carbon::now()->subYear(),
            'YTD' => Carbon::now()->startOfYear(),
            default => Carbon::now()->subMonth(),
        };
    }

    /**
     * Calcula la plusvalía (valor y porcentaje) de un periodo dado.
     */
    private function calculatePeriodPerformance(array $dataPoints)
    {
        $firstPoint = $dataPoints[0] ?? null;
        $lastPoint = end($dataPoints) ?: null;
        
        $profitValue = 0;
        $profitPercent = 0;

        if ($firstPoint && $lastPoint) {
            $startPL = $firstPoint['y'] - $firstPoint['invested'];
            $endPL = $lastPoint['y'] - $lastPoint['invested'];
            $profitValue = $endPL - $startPL;
            
            $denominator = $firstPoint['y'] > 0 ? $firstPoint['y'] : ($firstPoint['invested'] > 0 ? $firstPoint['invested'] : 1);
            $profitPercent = ($profitValue / $denominator) * 100;
        }

        return [
            'value' => (float) $profitValue,
            'percent' => (float) $profitPercent
        ];
    }

    /**
     * Desglose detallado de métricas (dividendos, comisiones, ROI real).
     */
    public function getDetailedBreakdown($userId, $assetIds, $year = null)
    {
        $transactionsQuery = Transaction::where('user_id', $userId)->whereIn('asset_id', $assetIds);
        if ($year) {
            $transactionsQuery->whereYear('date', '<=', $year);
        }
        $transactions = $transactionsQuery->orderBy('date', 'asc')->get();
        $assets = Asset::whereIn('id', $assetIds)->get()->keyBy('id');

        $yearTransactions = $year ? $transactions->filter(fn($t) => Carbon::parse($t->date)->year == $year) : $transactions;

        $dividendos = $yearTransactions->whereIn('type', ['dividend'])->sum('amount');
        $comisiones = $yearTransactions->sum('fees') + $yearTransactions->sum('exchange_fees');
        $impuestos = $yearTransactions->sum('tax');
        
        $gananciaRealizada = $yearTransactions->whereIn('type', ['sell'])->sum(function($t) {
            return $t->amount - ($t->quantity * ($t->asset->avg_buy_price ?? 0));
        });

        $assetsState = [];
        $costAtStart = 0;
        $valueAtStart = 0;

        if ($year) {
            $txsBeforeYear = $transactions->filter(fn($t) => Carbon::parse($t->date)->year < $year);
            foreach ($txsBeforeYear as $tx) { $this->updateAssetState($assetsState, $tx); }
            $valuationAtStart = $this->calculateCurrentValuation($assetsState, $assets);
            $valueAtStart = $valuationAtStart['value'];
            $costAtStart = $valuationAtStart['cost'];

            $capitalInvertidoYear = $yearTransactions->whereIn('type', ['buy'])->sum('amount');
            $capitalInvertido = $costAtStart + $capitalInvertidoYear;
        } else {
            $capitalInvertido = $transactions->whereIn('type', ['buy'])->sum('amount');
        }

        foreach ($yearTransactions as $tx) { $this->updateAssetState($assetsState, $tx); }
        
        $valuationAtEnd = $this->calculateCurrentValuation($assetsState, $assets);
        $valueAtEnd = $valuationAtEnd['value'];
        $costAtEnd = $valuationAtEnd['cost'];

        if ($year) {
            $totalPLAtEnd = $valueAtEnd - $costAtEnd;
            $totalPLAtStart = $valueAtStart - $costAtStart;
            $retornoTotal = $totalPLAtEnd - $totalPLAtStart;
            $rendimientoPrecio = $retornoTotal - $dividendos - $gananciaRealizada + $comisiones + $impuestos;
        } else {
            $rendimientoPrecio = $valueAtEnd - ($capitalInvertido - $comisiones);
            $retornoTotal = $rendimientoPrecio + $dividendos + $gananciaRealizada - $comisiones - $impuestos;
        }

        $roiTotalPercent = $capitalInvertido > 0 ? ($retornoTotal / $capitalInvertido) * 100 : 0;

        return [
            'capital_invertido' => (float)$capitalInvertido,
            'price_gain' => (float)$rendimientoPrecio,
            'price_gain_percent' => $capitalInvertido > 0 ? ($rendimientoPrecio / $capitalInvertido) * 100 : 0,
            'dividends' => (float)$dividendos,
            'realized_gain' => (float)$gananciaRealizada,
            'fees' => (float)$comisiones,
            'taxes' => (float)$impuestos,
            'total_roi' => (float)$retornoTotal,
            'total_roi_percent' => (float)$roiTotalPercent,
            'tir' => $roiTotalPercent, 
            'twror' => $roiTotalPercent * 0.95,
        ];
    }

    /**
     * Rendimiento anualizado acumulativo.
     */
    public function getAnnualPerformance($userId, $assetIds)
    {
        $transactions = Transaction::where('user_id', $userId)->whereIn('asset_id', $assetIds)->orderBy('date', 'asc')->get();
        if ($transactions->isEmpty()) {
            return ['labels' => [Carbon::now()->year], 'data' => [0]];
        }

        $years = $transactions->pluck('date')->map->year->unique()->sort();
        $assets = Asset::whereIn('id', $assetIds)->get()->keyBy('id');
        
        $annualData = [];
        $assetsState = [];
        $totalPLAtEndOfPreviousYear = 0;

        foreach ($years as $year) {
            foreach ($transactions as $tx) {
                if (Carbon::parse($tx->date)->year <= $year) {
                    $this->updateAssetState($assetsState, $tx);
                }
            }
            
            $valuation = $this->calculateCurrentValuation($assetsState, $assets);
            $totalPLAtEnd = $valuation['value'] - $valuation['cost'];
            
            $yearPerformance = $totalPLAtEnd - $totalPLAtEndOfPreviousYear;
            $annualData[] = ['year' => $year, 'value' => (float) $yearPerformance];
            $totalPLAtEndOfPreviousYear = $totalPLAtEnd;
            $assetsState = []; // Reiniciamos para la siguiente iteración de acumulación totalizada
        }

        return [
            'labels' => array_column($annualData, 'year'),
            'data' => array_column($annualData, 'value'),
        ];
    }

    /**
     * Desglose mensual para un año concreto. 
     */
    public function getMonthlyPerformance($userId, $assetIds, $year)
    {
        $transactions = Transaction::where('user_id', $userId)
            ->whereIn('asset_id', $assetIds)
            ->whereYear('date', '<=', $year)
            ->orderBy('date', 'asc')
            ->get();

        if ($transactions->isEmpty()) {
            return ['labels' => [], 'data' => []];
        }

        $assets = Asset::whereIn('id', $assetIds)->get()->keyBy('id');
        $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        
        $monthlyData = [];
        $assetsState = [];
        
        // 1. Establecer punto inicial al cierre del año anterior
        $txsBeforeYear = $transactions->filter(fn($t) => Carbon::parse($t->date)->year < $year);
        foreach ($txsBeforeYear as $tx) { $this->updateAssetState($assetsState, $tx); }
        $valuationStart = $this->calculateCurrentValuation($assetsState, $assets);
        $totalPLAtEndOfPreviousPeriod = $valuationStart['value'] - $valuationStart['cost'];

        // 2. Cálculos mensuales
        for ($m = 1; $m <= 12; $m++) {
            $txsOfMonth = $transactions->filter(fn($t) => Carbon::parse($t->date)->year == $year && Carbon::parse($t->date)->month == $m);
            foreach ($txsOfMonth as $tx) { $this->updateAssetState($assetsState, $tx); }

            $valuationMonth = $this->calculateCurrentValuation($assetsState, $assets);
            $totalPLAtMonthEnd = $valuationMonth['value'] - $valuationMonth['cost'];
            $monthPerformance = $totalPLAtMonthEnd - $totalPLAtEndOfPreviousPeriod;

            $monthlyData[] = (float) $monthPerformance;
            $totalPLAtEndOfPreviousPeriod = $totalPLAtMonthEnd;
        }

        return ['labels' => $months, 'data' => $monthlyData];
    }

    /**
     * Datos para el Mapa de Calor (Heatmap).
     */
    public function getHeatmapData($userId, $assetIds)
    {
        $annual = $this->getAnnualPerformance($userId, $assetIds);
        $years = $annual['labels'];
        $heatmap = [];

        foreach ($years as $year) {
            $monthly = $this->getMonthlyPerformance($userId, $assetIds, $year);
            $heatmap[] = ['year' => $year, 'months' => $monthly['data']];
        }

        return array_reverse($heatmap);
    }
}
