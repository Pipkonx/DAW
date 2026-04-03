<?php

namespace App\Services\Analysis\Concerns;

use App\Models\Transaction;
use Carbon\Carbon;

/**
 * Trait para centralizar los cálculos de valoración de activos y el algoritmo WAC.
 */
trait ManagesAssetState
{
    /**
     * Actualiza el estado de cantidad y coste base de un activo usando el método WAC.
     * 
     * EL ALGORITMO WAC (Weighted Average Cost - Coste Medio Ponderado):
     * 1. Las COMPRAS (buy) incrementan la cantidad y el coste base en la cantidad pagada.
     * 2. Las VENTAS (sell) reducen la cantidad y el coste base de forma proporcional. 
     *    El coste base se reduce restando (Precio Medio de Compra x Cantidad Vendida).
     * Esto asegura que las plusvalías se calculen correctamente tras múltiples ciclos de compra/venta.
     * 
     * @param array $state Estado acumulado de [qty, costBasis] por asset_id
     * @param Transaction $tx Transacción a procesar
     */
    protected function updateAssetState(&$state, Transaction $tx)
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
            
            // Sanitización por errores de redondeo (float precision)
            if ($state[$assetId]['qty'] < 0.00000001) {
                $state[$assetId]['qty'] = 0;
                $state[$assetId]['costBasis'] = 0;
            }
        }
    }

    /**
     * Calcula la valoración actual y el coste base acumulado para un estado de activos dado.
     * 
     * @param array $assetsState [asset_id => [qty, costBasis]]
     * @param \Illuminate\Support\Collection $assets Colección de activos con current_price
     * @return array [value, cost]
     */
    protected function calculateCurrentValuation(array $assetsState, $assets)
    {
        $value = 0;
        $cost = 0;

        foreach ($assetsState as $assetId => $state) {
            if ($state['qty'] > 0) {
                $asset = $assets->get($assetId);
                // Usamos el precio actual (current_price) para la valoración en tiempo real
                $value += $state['qty'] * ($asset->current_price ?? 0);
                $cost += $state['costBasis'];
            }
        }

        return ['value' => $value, 'cost' => $cost];
    }
}
