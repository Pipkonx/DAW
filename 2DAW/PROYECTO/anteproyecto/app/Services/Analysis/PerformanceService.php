<?php

namespace App\Services\Analysis;

use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PerformanceService
{
    /**
     * Calcula la distribución de activos (Asset Allocation) agrupada por un campo específico.
     * 
     * @param \Illuminate\Support\Collection $assets
     * @param string $field Campo base para agrupar (ej. sector, moneda, tipo)
     * @return \Illuminate\Support\Collection
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
     * 
     * @param int $userId
     * @param array $assetIds IDs de los activos a incluir
     * @param string $timeframe Rango temporal (1M, 3M, 1Y, MAX...)
     * @param float $currentTotalValue Valor actual de mercado
     * @return array
     */
    public function getChartData($userId, $assetIds, $timeframe, $currentTotalValue)
    {
        $endDate = Carbon::now();
        $startDate = $this->getStartDate($userId, $assetIds, $timeframe);

        // Obtenemos TODAS las transacciones para trazar el historial de coste base (WAC) 
        // correctamente desde el origen de los tiempos.
        $allTransactions = Transaction::where('user_id', $userId)
            ->whereIn('asset_id', $assetIds)
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
            
        // Agrupamos por fecha para optimizar la iteración diaria
        $transactionsByDate = $allTransactions->groupBy(fn($t) => substr($t->date, 0, 10));
        
        $period = CarbonPeriod::create($startDate, '1 day', $endDate);
        
        // Estado temporal de cada activo (cantidad y coste base)
        $assetsState = [];
        $assets = \App\Models\Asset::whereIn('id', $assetIds)->get()->keyBy('id');
        
        // 1. Procesamos transacciones ANTERIORES a la fecha de inicio para establecer el punto inicial
        foreach ($allTransactions as $tx) {
            $txDate = Carbon::parse($tx->date);
            if ($txDate->lt($startDate)) {
                $this->updateAssetState($assetsState, $tx);
            }
        }
        
        // 2. Iteramos por cada día del periodo calculando valoraiones diarias
        $dataPoints = [];
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            
            // Actualizamos estado con las transacciones de hoy
            if (isset($transactionsByDate[$dateStr])) {
                foreach ($transactionsByDate[$dateStr] as $tx) {
                    $this->updateAssetState($assetsState, $tx);
                }
            }
            
            // Calculamos valor total y coste base en este punto temporal
            $dailyValue = 0;
            $dailyCost = 0;

            foreach ($assetsState as $assetId => $state) {
                if ($state['qty'] > 0) {
                    $asset = $assets->get($assetId);
                    // Usamos current_price (estimación) a falta de histórico diario de precios Spot
                    $dailyValue += $state['qty'] * ($asset->current_price ?? 0);
                    $dailyCost += $state['costBasis'];
                }
            }

            $dataPoints[] = [
                'x' => $dateStr,
                'y' => round($dailyValue, 2),
                'invested' => round($dailyCost, 2)
            ];
        }

        // Calculamos el rendimiento del periodo (Plusvalía absoluta y relativa)
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
     * Actualiza el estado de cantidad y coste base de un activo usando el método WAC.
     * 
     * EL ALGORITMO WAC (Weighted Average Cost - Coste Medio Ponderado):
     * 1. Las COMPRAS (buy) incrementan la cantidad y el coste base en la cantidad pagada.
     * 2. Las VENTAS (sell) reducen la cantidad y el coste base de forma proporcional. 
     *    El coste base se reduce restando (Precio Medio de Compra x Cantidad Vendida).
     * Esto asegura que las plusvalías se calculen correctamente tras múltiples ciclos de compra/venta.
     */
    private function updateAssetState(&$state, $tx)
    {
        $assetId = $tx->asset_id;

        if (!isset($state[$assetId])) {
            $state[$assetId] = ['qty' => 0, 'costBasis' => 0];
        }
        
        if (in_array($tx->type, ['buy', 'transfer_in', 'gift', 'reward'])) {
            // Entrada de activos: Incremento lineal del coste
            $state[$assetId]['costBasis'] += $tx->quantity * $tx->price_per_unit;
            $state[$assetId]['qty'] += $tx->quantity;
        } elseif (in_array($tx->type, ['sell', 'transfer_out'])) {
            // Salida de activos: Reducción proporcional según coste medio previo
            if ($state[$assetId]['qty'] > 0) {
                $averageCost = $state[$assetId]['costBasis'] / $state[$assetId]['qty'];
                $state[$assetId]['costBasis'] -= $averageCost * $tx->quantity;
            }
            $state[$assetId]['qty'] -= $tx->quantity;
            
            // Sanitización por errores de redondeo
            if ($state[$assetId]['qty'] < 0.00000001) {
                $state[$assetId]['qty'] = 0;
                $state[$assetId]['costBasis'] = 0;
            }
        }
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
     * Calcula la plusvalía (valor y porcentaje) lograda durante el periodo seleccionado.
     * 
     * NOTA: El rendimiento se basa en la diferencia de plusvalías entre el primer y último punto.
     */
    private function calculatePeriodPerformance(array $dataPoints)
    {
        $firstPoint = $dataPoints[0] ?? null;
        $lastPoint = end($dataPoints) ?: null;
        
        $profitValue = 0;
        $profitPercent = 0;

        if ($firstPoint && $lastPoint) {
            // Plusvalía inicial y final (Valor - Coste)
            $startPL = $firstPoint['y'] - $firstPoint['invested'];
            $endPL = $lastPoint['y'] - $lastPoint['invested'];
            
            $profitValue = $endPL - $startPL;
            
            // Rentabilidad relativa sobre el valor total inicial
            $denominator = $firstPoint['y'] > 0 ? $firstPoint['y'] : ($firstPoint['invested'] > 0 ? $firstPoint['invested'] : 1);
            $profitPercent = ($profitValue / $denominator) * 100;
        }

        return [
            'value' => (float) $profitValue,
            'percent' => (float) $profitPercent
        ];
    }

    /**
     * Calcula un desglose detallado de métricas de rendimiento.
     */
    public function getDetailedBreakdown($userId, $assetIds, $year = null)
    {
        $transactionsQuery = Transaction::where('user_id', $userId)
            ->whereIn('asset_id', $assetIds);

        if ($year) {
            $transactionsQuery->whereYear('date', '<=', $year);
        }
        $transactions = $transactionsQuery->orderBy('date', 'asc')->get();

        $assets = \App\Models\Asset::whereIn('id', $assetIds)->get()->keyBy('id');

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
            foreach ($txsBeforeYear as $tx) {
                $this->updateAssetState($assetsState, $tx);
            }
            foreach ($assetsState as $assetId => $state) {
                if ($state['qty'] > 0) {
                    $asset = $assets->get($assetId);
                    $valueAtStart += $state['qty'] * ($asset->current_price ?? 0);
                    $costAtStart += $state['costBasis'];
                }
            }
            $capitalInvertidoYear = $yearTransactions->whereIn('type', ['buy'])->sum('amount');
            $capitalInvertido = $costAtStart + $capitalInvertidoYear;
        } else {
            $capitalInvertido = $transactions->whereIn('type', ['buy'])->sum('amount');
        }

        foreach ($yearTransactions as $tx) {
            $this->updateAssetState($assetsState, $tx);
        }
        
        $costAtEnd = 0;
        $valueAtEnd = 0;
        foreach ($assetsState as $assetId => $state) {
            if ($state['qty'] > 0) {
                $asset = $assets->get($assetId);
                $valueAtEnd += $state['qty'] * ($asset->current_price ?? 0);
                $costAtEnd += $state['costBasis'];
            }
        }

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
     * Obtiene el rendimiento anualizado (Plusvalía absoluta por año).
     * Calcula el diferencial de (Valor Total - Coste Base) entre el inicio y fin de cada año.
     */
    public function getAnnualPerformance($userId, $assetIds)
    {
        $transactions = Transaction::where('user_id', $userId)
            ->whereIn('asset_id', $assetIds)
            ->orderBy('date', 'asc')
            ->get();

        if ($transactions->isEmpty()) {
            return ['labels' => [Carbon::now()->year], 'data' => [0]];
        }

        $years = $transactions->pluck('date')->map->year->unique()->sort();
        $assets = \App\Models\Asset::whereIn('id', $assetIds)->get()->keyBy('id');
        
        $annualData = [];
        $assetsState = [];
        $totalPLAtEndOfPreviousYear = 0;

        foreach ($years as $year) {
            // 1. Procesar transacciones hasta el final de este año (31 Dic)
            foreach ($transactions as $tx) {
                if (Carbon::parse($tx->date)->year <= $year) {
                    $this->updateAssetState($assetsState, $tx);
                    // IMPORTANTE: También necesitamos rastrear dividendos y ventas realizadas como P/L histórico
                }
            }
            
            // 2. Calcular Valor y Coste al final del año
            $valueAtEnd = 0;
            $costAtEnd = 0;
            foreach ($assetsState as $assetId => $state) {
                if ($state['qty'] > 0) {
                    $asset = $assets->get($assetId);
                    $valueAtEnd += $state['qty'] * ($asset->current_price ?? 0);
                    $costAtEnd += $state['costBasis'];
                }
            }

            // 3. Plusvalía acumulada al final de este año
            $totalPLAtEnd = $valueAtEnd - $costAtEnd;
            
            // 4. Rendimiento del año = PL_actual - PL_anterior
            $yearPerformance = $totalPLAtEnd - $totalPLAtEndOfPreviousYear;
            
            $annualData[] = [
                'year' => $year,
                'value' => (float) $yearPerformance
            ];

            // Preparar para el siguiente año
            $totalPLAtEndOfPreviousYear = $totalPLAtEnd;
            // Reiniciamos el estado para recalcular correctamente o seguimos acumulando (WAC acumula)
            $assetsState = []; // Reset para evitar duplicar transacciones en la siguiente iteración
        }

        return [
            'labels' => array_column($annualData, 'year'),
            'data' => array_column($annualData, 'value'),
        ];
    }

    /**
     * Obtiene el rendimiento mensual detallado de un año concreto. 
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

        $assets = \App\Models\Asset::whereIn('id', $assetIds)->get()->keyBy('id');
        $months = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        
        $monthlyData = [];
        $assetsState = [];
        $totalPLAtEndOfPreviousPeriod = 0;

        // 1. Establecer el punto inicial (P/L acumulado al final del año anterior)
        $txsBeforeYear = $transactions->filter(fn($t) => Carbon::parse($t->date)->year < $year);
        foreach ($txsBeforeYear as $tx) {
            $this->updateAssetState($assetsState, $tx);
        }

        $valueAtStart = 0;
        $costAtStart = 0;
        foreach ($assetsState as $assetId => $state) {
            if ($state['qty'] > 0) {
                $asset = $assets->get($assetId);
                $valueAtStart += $state['qty'] * ($asset->current_price ?? 0);
                $costAtStart += $state['costBasis'];
            }
        }
        $totalPLAtEndOfPreviousPeriod = $valueAtStart - $costAtStart;

        // 2. Calcular mes a mes del año seleccionado
        for ($m = 1; $m <= 12; $m++) {
            $txsOfMonth = $transactions->filter(fn($t) => Carbon::parse($t->date)->year == $year && Carbon::parse($t->date)->month == $m);
            foreach ($txsOfMonth as $tx) {
                $this->updateAssetState($assetsState, $tx);
            }

            $valueAtMonthEnd = 0;
            $costAtMonthEnd = 0;
            foreach ($assetsState as $assetId => $state) {
                if ($state['qty'] > 0) {
                    $asset = $assets->get($assetId);
                    $valueAtMonthEnd += $state['qty'] * ($asset->current_price ?? 0);
                    $costAtMonthEnd += $state['costBasis'];
                }
            }

            $totalPLAtMonthEnd = $valueAtMonthEnd - $costAtMonthEnd;
            $monthPerformance = $totalPLAtMonthEnd - $totalPLAtEndOfPreviousPeriod;

            $monthlyData[] = (float) $monthPerformance;
            $totalPLAtEndOfPreviousPeriod = $totalPLAtMonthEnd;

            // Si es un mes futuro del año actual y no hay datos, podríamos detenernos o poner 0
            if ($year == Carbon::now()->year && $m > Carbon::now()->month) {
                // Opcional: Break o llenar con 0
            }
        }

        return [
            'labels' => $months,
            'data' => $monthlyData,
        ];
    }

    /**
     * Obtiene los datos para el Mapa de Calor (Heatmap) de rendimiento.
     * Retorna una matriz [Año][Mes] = Rendimiento.
     */
    public function getHeatmapData($userId, $assetIds)
    {
        $annual = $this->getAnnualPerformance($userId, $assetIds);
        $years = $annual['labels'];
        $heatmap = [];

        foreach ($years as $year) {
            $monthly = $this->getMonthlyPerformance($userId, $assetIds, $year);
            $heatmap[] = [
                'year' => $year,
                'months' => $monthly['data']
            ];
        }

        return array_reverse($heatmap); // Años más recientes primero
    }
}
