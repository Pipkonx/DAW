<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EvaluacionDetalle extends Model
{
    protected $fillable = [
        'evaluacion_id',
        'capacidad_id',
        'nota',
    ];

    public function evaluacion(): BelongsTo
    {
        return $this->belongsTo(Evaluacion::class);
    }

    public function capacidad(): BelongsTo
    {
        return $this->belongsTo(CapacidadEvaluacion::class, 'capacidad_id');
    }
}
