<?php

namespace App\Services\Transaction;

use App\Models\Transaction;
use App\Models\Asset;
use App\Services\Asset\AssetService;
use App\Services\MarketDataService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    protected $assetService;
    protected $marketDataService;

    public function __construct(AssetService $assetService, MarketDataService $marketDataService)
    {
        $this->assetService = $assetService;
        $this->marketDataService = $marketDataService;
    }

    /**
     * Registra una nueva transacción y actualiza las métricas del activo relacionado.
     * 
     * @param array $data Datos de la transacción
     * @return \App\Models\Transaction
     */
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $userId = Auth::id();
            $assetId = null;

            if (in_array($data['type'], ['buy', 'sell', 'dividend', 'reward', 'gift'])) {
                $asset = $this->assetService->findOrCreateAndLink($userId, [
                    'portfolio_id' => $data['portfolio_id'] ?? null,
                    'ticker' => $data['asset_name'],
                    'name' => $data['asset_full_name'] ?? null,
                    'type' => $data['asset_type'] ?? 'stock',
                    'isin' => $data['isin'] ?? null,
                    'market_asset_id' => $data['market_asset_id'] ?? null,
                    'price_per_unit' => $data['price_per_unit'] ?? 0,
                    'currency_code' => $data['currency_code'] ?? 'EUR',
                ]);

                $assetId = $asset->id;

                // Actualizamos la cantidad y los precios del activo según el tipo de movimiento
                if (in_array($data['type'], ['buy', 'reward', 'gift'])) {
                    $this->updateAssetOnBuy($asset, $data['quantity'] ?? 0, $data['price_per_unit'] ?? 0);
                } elseif ($data['type'] === 'sell') {
                    $this->updateAssetOnSell($asset, $data['quantity'] ?? 0, $data['price_per_unit'] ?? 0);
                }
            }

            $resolvedCategoryId = $this->resolveCategory(
                $userId, 
                $data['type'], 
                $data['category_id'] ?? null, 
                $data['category_name'] ?? null
            );

            $transaction = Transaction::create([
                'user_id' => $userId,
                'asset_id' => $assetId,
                'type' => $data['type'],
                'amount' => $data['amount'],
                'date' => $data['date'],
                'category_id' => $resolvedCategoryId,
                'description' => $data['description'] ?? null,
                'quantity' => $data['quantity'] ?? null,
                'price_per_unit' => $data['price_per_unit'] ?? null,
                'portfolio_id' => $data['portfolio_id'] ?? null,
                'fees' => $data['fees'] ?? null,
                'exchange_fees' => $data['exchange_fees'] ?? null,
                'tax' => $data['tax'] ?? null,
                'currency' => $data['currency_code'] ?? 'EUR',
                'time' => $data['time'] ?? null,
            ]);

            $this->incrementCategoryUsage($resolvedCategoryId);

            return $transaction;
        });
    }

    /**
     * Actualiza una transacción existente y ajusta las métricas si hay cambios en las cantidades.
     */
    public function update(Transaction $transaction, array $data)
    {
        return DB::transaction(function () use ($transaction, $data) {
            if ($transaction->asset_id && in_array($transaction->type, ['buy', 'sell', 'reward', 'gift'])) {
                $asset = $transaction->asset;
                $diff = ($data['quantity'] ?? $transaction->quantity) - $transaction->quantity;
                
                if ($diff != 0) {
                    if (in_array($transaction->type, ['buy', 'reward', 'gift'])) $asset->quantity += $diff;
                    elseif ($transaction->type === 'sell') $asset->quantity -= $diff;
                    $asset->save();
                }
            }

            $resolvedCategoryId = $this->resolveCategory(
                $transaction->user_id, 
                $transaction->type, 
                $data['category_id'] ?? null, 
                $data['category_name'] ?? null
            );

            $transaction->update([
                'amount' => $data['amount'],
                'date' => $data['date'],
                'category_id' => $resolvedCategoryId,
                'description' => $data['description'] ?? null,
                'quantity' => $data['quantity'] ?? $transaction->quantity,
                'price_per_unit' => $data['price_per_unit'] ?? $transaction->price_per_unit,
                'fees' => $data['fees'] ?? null,
                'exchange_fees' => $data['exchange_fees'] ?? null,
                'tax' => $data['tax'] ?? null,
                'currency' => $data['currency_code'] ?? $transaction->currency,
                'time' => $data['time'] ?? null,
            ]);

            if ($transaction->asset_id && isset($data['currency_code'])) {
                $transaction->asset->update(['currency_code' => $data['currency_code']]);
            }

            return $transaction;
        });
    }

    /**
     * Elimina una transacción y recalcula o limpia las métricas del activo asociado.
     */
    public function delete(Transaction $transaction)
    {
        return DB::transaction(function () use ($transaction) {
            $asset = $transaction->asset;
            $transaction->delete();

            if ($asset) {
                if ($asset->transactions()->count() === 0) {
                    $asset->delete();
                } else {
                    $asset->recalculateMetrics();
                }
            }
        });
    }

    private function updateAssetOnBuy(Asset $asset, $qty, $price)
    {
        $currentTotalVal = $asset->quantity * $asset->avg_buy_price;
        $newBuyVal = $qty * $price;
        $newTotalQty = $asset->quantity + $qty;
        
        if ($newTotalQty > 0) {
            $asset->avg_buy_price = ($currentTotalVal + $newBuyVal) / $newTotalQty;
        }
        
        $asset->quantity = $newTotalQty;
        if ($price > 0) $asset->current_price = $price;
        $asset->save();
    }

    private function updateAssetOnSell(Asset $asset, $qty, $price)
    {
        $asset->quantity = max(0, $asset->quantity - $qty);
        if ($price > 0) $asset->current_price = $price;
        $asset->save();
    }

    private function incrementCategoryUsage($categoryId)
    {
        if (!$categoryId) return;
        $category = \App\Models\Category::find($categoryId);
        if ($category) {
            $category->increment('usage_count');
            if ($category->parent_id) {
                \App\Models\Category::where('id', $category->parent_id)->increment('usage_count');
            }
        }
    }

    private function resolveCategory($userId, $type, $categoryId, $categoryName)
    {
        if ($categoryId) return $categoryId;
        if (!$categoryName) return null;

        // Limitar a income/expense
        $catType = in_array($type, ['income', 'expense']) ? $type : 'expense';

        // Buscar exacta ignorando mayúsculas
        $category = \App\Models\Category::where('user_id', $userId)
                        ->where('type', $catType)
                        ->where('name', 'LIKE', $categoryName)
                        ->first();
                        
        if ($category) return $category->id;
        
        // Crear si no existe
        $newCategory = \App\Models\Category::create([
            'user_id' => $userId,
            'name' => $categoryName,
            'type' => $catType,
            'color' => '#64748b', // Por defecto un color neutro
            'is_active' => true,
        ]);
        
        return $newCategory->id;
    }
}
