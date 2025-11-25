<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $table = 'transacciones';
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'fecha',
        'monto',
        'descripcion',
        'tipo',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la categoría (castellano)
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'category_id');
    }

    /**
     * Alias en inglés para compatibilidad
     */
    public function category()
    {
        return $this->categoria();
    }
}
