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
     * Store a new transaction and update asset metrics.
     */
    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $userId = Auth::id();
            $assetId = null;

            if (in_array($data['type'], ['buy', 'sell', 'dividend'])) {
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

                // Update asset quantity and prices
                if ($data['type'] === 'buy') {
                    $this->updateAssetOnBuy($asset, $data['quantity'] ?? 0, $data['price_per_unit'] ?? 0);
                } elseif ($data['type'] === 'sell') {
                    $this->updateAssetOnSell($asset, $data['quantity'] ?? 0, $data['price_per_unit'] ?? 0);
                }
            }

            $transaction = Transaction::create([
                'user_id' => $userId,
                'asset_id' => $assetId,
                'type' => $data['type'],
                'amount' => $data['amount'],
                'date' => $data['date'],
                'category_id' => $data['category_id'] ?? null,
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

            $this->incrementCategoryUsage($data['category_id'] ?? null);

            return $transaction;
        });
    }

    /**
     * Update an existing transaction.
     */
    public function update(Transaction $transaction, array $data)
    {
        return DB::transaction(function () use ($transaction, $data) {
            if ($transaction->asset_id && in_array($transaction->type, ['buy', 'sell'])) {
                $asset = $transaction->asset;
                $diff = ($data['quantity'] ?? $transaction->quantity) - $transaction->quantity;
                
                if ($diff != 0) {
                    if ($transaction->type === 'buy') $asset->quantity += $diff;
                    elseif ($transaction->type === 'sell') $asset->quantity -= $diff;
                    $asset->save();
                }
            }

            $transaction->update([
                'amount' => $data['amount'],
                'date' => $data['date'],
                'category_id' => $data['category_id'] ?? null,
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
     * Delete a transaction and clean up asset metrics.
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
}
