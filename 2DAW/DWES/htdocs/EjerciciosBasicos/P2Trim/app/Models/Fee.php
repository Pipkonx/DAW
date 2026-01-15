<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $fillable = [
        'client_id', 'concept', 'emission_date', 'amount', 
        'is_paid', 'payment_date', 'notes', 'invoice_path'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
