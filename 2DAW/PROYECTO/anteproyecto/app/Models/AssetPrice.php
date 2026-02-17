<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'market_asset_id',
        'date',
        'price',
        'volume',
        'source',
    ];

    protected $casts = [
        'date' => 'date',
        'price' => 'decimal:8',
        'volume' => 'integer',
    ];

    public function marketAsset()
    {
        return $this->belongsTo(MarketAsset::class);
    }
}
