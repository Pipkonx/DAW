<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'portfolio_id',
        'asset_id',
        'type',
        'amount',
        'date',
        'category_id',
        'description',
        'quantity',
        'price_per_unit',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'quantity' => 'decimal:8',
        'price_per_unit' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
