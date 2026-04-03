<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamousInvestorHolding extends Model
{
    use HasFactory;

    protected $fillable = [
        'famous_investor_id', 'symbol', 'name', 'shares_number', 'market_value', 'weight'
    ];

    public function investor()
    {
        return $this->belongsTo(FamousInvestor::class, 'famous_investor_id');
    }
}
