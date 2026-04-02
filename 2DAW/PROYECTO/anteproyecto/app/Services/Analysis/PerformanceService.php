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

        // Fetch ALL transactions for the included assets to trace cost basis correctly from the beginning
        $allTransactions = Transaction::where('user_id', $userId)
            ->whereIn('asset_id', $assetIds)
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
            
        // Group transactions by date for efficient iteration
        $txByDate = $allTransactions->groupBy(fn($t) => substr($t->date, 0, 10));
        
        // Timeframe dates
        $period = CarbonPeriod::create($startDate, '1 day', $endDate);
        
        // Tracking state of each asset (quantity and cost basis)
        $assetsState = [];
        $assets = \App\Models\Asset::whereIn('id', $assetIds)->get()->keyBy('id');
        
        // 1. Process transactions BEFORE the start date to set the initial point
        foreach ($allTransactions as $t) {
            $tDate = Carbon::parse($t->date);
            if ($tDate->lt($startDate)) {
                $this->updateAssetState($assetsState, $t);
            }
        }
        
        // 2. Iterate through the period calculating daily values
        $dataPoints = [];
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            
            // Update state with today's transactions
            if (isset($txByDate[$dateStr])) {
                foreach ($txByDate[$dateStr] as $t) {
                    $this->updateAssetState($assetsState, $t);
                }
            }
            
            // Calculate total value and cost at this point in time
            $dailyValue = 0;
            $dailyCost = 0;
            foreach ($assetsState as $assetId => $state) {
                if ($state['qty'] > 0) {
                    $asset = $assets->get($assetId);
                    // Use current_price for now (ideally use historical price from AssetPrice)
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
     * Internal helper to update asset quantity and cost basis using WAC.
     */
    private function updateAssetState(&$state, $tx)
    {
        if (!isset($state[$tx->asset_id])) {
            $state[$tx->asset_id] = ['qty' => 0, 'costBasis' => 0];
        }
        
        if (in_array($tx->type, ['buy', 'transfer_in', 'gift', 'reward'])) {
            $state[$tx->asset_id]['costBasis'] += $tx->quantity * $tx->price_per_unit;
            $state[$tx->asset_id]['qty'] += $tx->quantity;
        } elseif (in_array($tx->type, ['sell', 'transfer_out'])) {
            if ($state[$tx->asset_id]['qty'] > 0) {
                $avgCost = $state[$tx->asset_id]['costBasis'] / $state[$tx->asset_id]['qty'];
                $state[$tx->asset_id]['costBasis'] -= $avgCost * $tx->quantity;
            }
            $state[$tx->asset_id]['qty'] -= $tx->quantity;
        }
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
