<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticker',
        'api_id',
        'name',
        'isin',
        'type',
        'currency_code',
        'sector',
        'logo_url',
        'ter',
        'volume',
        'is_distributing',
        'country',
    ];

    protected $casts = [
        'ter' => 'float',
        'volume' => 'float',
        'is_distributing' => 'boolean',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the asset type label in Spanish
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'stock'  => 'Acciones',
            'etf'    => 'ETF',
            'crypto' => 'Criptomonedas',
            'fund'   => 'Fondos de inversión',
            'bond'   => 'Bonos',
            default  => 'Otros',
        };
    }
}
