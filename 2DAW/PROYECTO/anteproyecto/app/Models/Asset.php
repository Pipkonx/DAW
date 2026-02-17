<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'portfolio_id',
        'market_asset_id', // Added
        'name',
        'ticker',
        'isin', // Added
        'type',
        'sector',
        'industry',
        'region',
        'country',
        'currency_code',
        'quantity',
        'avg_buy_price',
        'current_price',
        'color',
    ];

    protected $appends = [
        'current_value',
        'total_invested',
        'profit_loss',
        'profit_loss_percentage',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function marketAsset()
    {
        return $this->belongsTo(MarketAsset::class);
    }

    // Calcula el valor total actual del activo
    public function getCurrentValueAttribute()
    {
        return $this->quantity * ($this->current_price ?? $this->avg_buy_price);
    }

    // Calcula el total invertido
    public function getTotalInvestedAttribute()
    {
        return $this->quantity * $this->avg_buy_price;
    }

    // Calcula ganancia/pérdida
    public function getProfitLossAttribute()
    {
        return $this->current_value - $this->total_invested;
    }

    public function getProfitLossPercentageAttribute()
    {
        if ($this->total_invested == 0) return 0;
        return ($this->profit_loss / $this->total_invested) * 100;
    }

    /**
     * Recalcula las métricas del activo basándose en su historial de transacciones.
     * Utiliza el método de Costo Promedio Ponderado (Weighted Average Cost).
     */
    public function recalculateMetrics()
    {
        $transactions = $this->transactions()->orderBy('date', 'asc')->orderBy('created_at', 'asc')->get();
        
        $currentQuantity = 0;
        $totalCost = 0; // Costo total acumulado
        
        foreach ($transactions as $tx) {
            if ($tx->type === 'buy' || $tx->type === 'transfer_in' || $tx->type === 'gift' || $tx->type === 'reward') {
                $txPrice = $tx->price_per_unit ?: 0;
                $txQuantity = $tx->quantity ?: 0;
                
                // Actualizamos el costo total y la cantidad
                $totalCost += $txQuantity * $txPrice;
                $currentQuantity += $txQuantity;
                
            } elseif ($tx->type === 'sell' || $tx->type === 'transfer_out') {
                $txQuantity = $tx->quantity ?: 0;
                
                if ($currentQuantity > 0) {
                    // Al vender, reducimos el costo total proporcionalmente
                    // CostoPromedio = TotalCost / CurrentQuantity
                    // CostoReducido = CostoPromedio * TxQuantity
                    $avgCost = $totalCost / $currentQuantity;
                    $totalCost -= $avgCost * $txQuantity;
                }
                
                $currentQuantity -= $txQuantity;
            }
        }
        
        // Evitar cantidades negativas por errores de datos
        $this->quantity = max(0, $currentQuantity);
        
        // Calcular precio promedio final
        if ($this->quantity > 0) {
            $this->avg_buy_price = $totalCost / $this->quantity;
        } else {
            $this->avg_buy_price = 0;
        }
        
        $this->save();
    }
}
