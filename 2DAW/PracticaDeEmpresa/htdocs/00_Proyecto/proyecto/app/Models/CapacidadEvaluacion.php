<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapacidadEvaluacion extends Model
{
    use SoftDeletes;

    protected $table = 'capacidad_evaluacions';

    protected $fillable = [
        'criterio_id',
        'nombre',
        'descripcion',
    ];

    public function criterio(): BelongsTo
    {
        return $this->belongsTo(CriterioEvaluacion::class, 'criterio_id');
    }
}
