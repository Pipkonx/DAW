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
        'name',
        'ticker',
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
}
