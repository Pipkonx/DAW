<?php

namespace App\Services\Analysis;

use App\Models\Transaction;
use App\Models\Asset;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * ValuationEngine - Motor de reconstrucción histórica de carteras.
 * 
 * Este servicio se encarga de "rebobinar" el historial de transacciones 
 * para determinar la composición y valor de una cartera en fechas pasadas.
 */
class ValuationEngine
{
    /**
     * Reconstruye la evolución diaria del valor y coste de una cartera.
     */
    public function getDailyHistoricalValuation($userId, array $assetIds, Carbon $startDate, Carbon $endDate): array
    {
        $allTransactions = Transaction::where('user_id', $userId)
            ->whereIn('asset_id', $assetIds)
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
            
        $transactionsByDate = $allTransactions->groupBy(fn($t) => substr($t->date, 0, 10));
        $days = $startDate->diffInDays($endDate);
        // Si el periodo es mayor a un año, iteramos mensualmente para no desbordar memoria.
        $isMonthly = $days > 365;
        
        $period = CarbonPeriod::create($startDate, $isMonthly ? '1 month' : '1 day', $endDate);
        
        $assets = Asset::whereIn('id', $assetIds)->get()->keyBy('id');
        $assetsState = [];
        
        // 1. Calcular estado inicial (Transacciones previas a la fecha de inicio)
        foreach ($allTransactions as $tx) {
            $txDate = Carbon::parse($tx->date);
            if ($txDate->lt($startDate)) {
                $this->updateAssetState($assetsState, $tx);
            }
        }
        
        // 2. Iteración sobre el periodo para construir el histórico
        $dataPoints = [];
        
        if ($isMonthly) {
            // Optimización para rangos masivos (e.g. desde 1970)
            $transactionsByMonth = $allTransactions->groupBy(fn($t) => substr($t->date, 0, 7));
            foreach ($period as $date) {
                // Avanzamos al último día del mes para consolidar
                $dateStr = $date->endOfMonth()->format('Y-m-d');
                $monthKey = $date->format('Y-m');
                
                if (isset($transactionsByMonth[$monthKey])) {
                    foreach ($transactionsByMonth[$monthKey] as $tx) {
                        $this->updateAssetState($assetsState, $tx);
                    }
                }
                
                $valuation = $this->calculateInstantValuation($assetsState, $assets);
                $dataPoints[] = [
                    'date' => $dateStr,
                    'value' => round($valuation['value'], 2),
                    'cost' => round($valuation['cost'], 2)
                ];
            }
        } else {
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                
                if (isset($transactionsByDate[$dateStr])) {
                    foreach ($transactionsByDate[$dateStr] as $tx) {
                        $this->updateAssetState($assetsState, $tx);
                    }
                }
                
                $valuation = $this->calculateInstantValuation($assetsState, $assets);
                $dataPoints[] = [
                    'date' => $dateStr,
                    'value' => round($valuation['value'], 2),
                    'cost' => round($valuation['cost'], 2)
                ];
            }
        }

        return $dataPoints;
    }

    /**
     * Actualiza el estado de cantidad y coste base (WAC - Weighted Average Cost).
     */
    public function updateAssetState(&$state, $tx): void
    {
        $assetId = $tx->asset_id;

        if (!isset($state[$assetId])) {
            $state[$assetId] = ['qty' => 0, 'costBasis' => 0];
        }
        
        if (in_array($tx->type, ['buy', 'transfer_in', 'gift', 'reward'])) {
            $state[$assetId]['costBasis'] += $tx->quantity * $tx->price_per_unit;
            $state[$assetId]['qty'] += $tx->quantity;
        } elseif (in_array($tx->type, ['sell', 'transfer_out'])) {
            if ($state[$assetId]['qty'] > 0) {
                $averageCost = $state[$assetId]['costBasis'] / $state[$assetId]['qty'];
                $state[$assetId]['costBasis'] -= $averageCost * $tx->quantity;
            }
            $state[$assetId]['qty'] -= $tx->quantity;
            
            if ($state[$assetId]['qty'] < 0.00000001) {
                $state[$assetId]['qty'] = 0;
                $state[$assetId]['costBasis'] = 0;
            }
        }
    }

    /**
     * Calcula la valoración instantánea basada en el estado actual de los activos.
     */
    public function calculateInstantValuation(array $assetsState, $assets): array
    {
        $value = 0;
        $cost = 0;

        foreach ($assetsState as $assetId => $state) {
            if ($state['qty'] > 0) {
                $asset = $assets->get($assetId);
                if ($asset) {
                    $value += $state['qty'] * ($asset->current_price ?? 0);
                    $cost += $state['costBasis'];
                }
            }
        }

        return ['value' => $value, 'cost' => $cost];
    }
}
