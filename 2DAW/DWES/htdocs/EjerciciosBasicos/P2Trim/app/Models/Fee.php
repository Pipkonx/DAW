<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    // Campos que se pueden rellenar en la tabla de Cuotas
    protected $fillable = [
        'client_id', 'concept', 'emission_date', 'amount', 
        'is_paid', 'payment_date', 'notes', 'invoice_path',
        'amount_eur', 'exchange_rate'
    ];

    // Relación: Una cuota pertenece obligatoriamente a un Cliente
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
