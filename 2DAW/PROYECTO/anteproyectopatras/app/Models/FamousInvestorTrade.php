<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamousInvestorTrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'famous_investor_id', 'symbol', 'name', 'change_in_shares', 'change_type', 'filling_date', 'percent_of_portfolio'
    ];

    public function investor()
    {
        return $this->belongsTo(FamousInvestor::class, 'famous_investor_id');
    }
}
