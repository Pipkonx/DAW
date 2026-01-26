<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CriterioEvaluacion extends Model
{
    use SoftDeletes;

    protected $table = 'criterio_evaluacions';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function capacidades(): HasMany
    {
        return $this->hasMany(CapacidadEvaluacion::class, 'criterio_id');
    }
}
