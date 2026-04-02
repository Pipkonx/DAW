<?php

namespace App\Services\Analysis;

use App\Models\Transaction;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PerformanceService
{
    /**
     * Calculates the asset allocation for a given set of assets by a specific field.
     */
    public function getAllocation($assets, $field)
    {
        return $assets->groupBy($field)
            ->map(function ($group, $key) {
                return [
                    'label' => $key ?: 'Otros',
                    'value' => (float) $group->sum('current_value'),
                    'color' => '#' . substr(md5($key), 0, 6)
                ];
            })->values();
    }

    /**
     * Generates chart data and calculates period performance for a set of assets.
     */
    public function getChartData($userId, $assetIds, $timeframe, $currentTotalValue)
    {
        $endDate = Carbon::now();
        $startDate = $this->getStartDate($userId, $assetIds, $timeframe);

        // Fetch transactions for flow calculation
        $allTransactions = Transaction::where('user_id', $userId)
            ->whereIn('asset_id', $assetIds)
            ->whereIn('type', ['buy', 'sell'])
            ->orderBy('date', 'asc')
            ->get();

        $invested = 0;
        
        // Initial invested amount before start date
        foreach($allTransactions as $t) {
            if (Carbon::parse($t->date)->lt($startDate)) {
                if ($t->type === 'buy') $invested += $t->amount;
                if ($t->type === 'sell') $invested -= $t->amount;
            }
        }

        $period = CarbonPeriod::create($startDate, '1 day', $endDate);
        $txByDate = $allTransactions->groupBy(fn($t) => substr($t->date, 0, 10));
        
        // Final invested amount calculation
        $finalInvested = $invested;
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            if (isset($txByDate[$dateStr])) {
                foreach ($txByDate[$dateStr] as $t) {
                    if ($t->type === 'buy') $finalInvested += $t->amount;
                    if ($t->type === 'sell') $finalInvested -= $t->amount;
                }
            }
        }
        
        $totalProfit = $currentTotalValue - $finalInvested;
        $totalDays = $startDate->diffInDays($endDate) ?: 1;
        
        $dataPoints = [];
        $currentInvested = $invested;
        $dayCounter = 0;

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            
            if (isset($txByDate[$dateStr])) {
                foreach ($txByDate[$dateStr] as $t) {
                    if ($t->type === 'buy') $currentInvested += $t->amount;
                    if ($t->type === 'sell') $currentInvested -= $t->amount;
                }
            }
            
            $dailyProfit = ($dayCounter / $totalDays) * $totalProfit;
            $estimatedValue = $currentInvested + $dailyProfit;

            $dataPoints[] = [
                'x' => $dateStr,
                'y' => max(0, $estimatedValue),
                'invested' => $currentInvested
            ];
            
            $dayCounter++;
        }

        // Calculate Period Performance
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
     * Helper to determine start date based on timeframe.
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
     * Calculates the profit/loss value and percentage for the period.
     */
    private function calculatePeriodPerformance(array $dataPoints)
    {
        $firstPoint = $dataPoints[0] ?? null;
        $lastPoint = end($dataPoints) ?: null;
        
        $value = 0;
        $percent = 0;

        if ($firstPoint && $lastPoint) {
            $startPL = $firstPoint['y'] - $firstPoint['invested'];
            $endPL = $lastPoint['y'] - $lastPoint['invested'];
            $value = $endPL - $startPL;
            
            $denominator = $firstPoint['y'] > 0 ? $firstPoint['y'] : ($firstPoint['invested'] > 0 ? $firstPoint['invested'] : 1);
            $percent = ($value / $denominator) * 100;
        }

        return [
            'value' => (float) $value,
            'percent' => (float) $percent
        ];
    }
}
