<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticker',
        'name',
        'isin',
        'type',
        'currency_code',
        'sector',
        'logo_url',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
