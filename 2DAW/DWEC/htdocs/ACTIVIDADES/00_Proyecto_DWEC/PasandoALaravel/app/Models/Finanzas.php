<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Finanzas extends Model
{
    use HasFactory;

    protected $table = 'finanzas';

    protected $fillable = [
        'user_id',
        'tipo',
        'monto',
        'descripcion',
        'fecha_registro',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
