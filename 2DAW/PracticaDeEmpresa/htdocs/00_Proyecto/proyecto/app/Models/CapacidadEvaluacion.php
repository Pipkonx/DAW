<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class CapacidadEvaluacion
 * @brief Modelo que representa una capacidad específica dentro de un criterio de evaluación.
 */
class CapacidadEvaluacion extends Model
{
    use SoftDeletes;

    protected $table = 'capacidad_evaluacions';

    protected $fillable = [
        'criterio_id',
        'nombre',
        'descripcion',
        'puntuacion_maxima',
        'activo',
    ];

    protected $casts = [
        'puntuacion_maxima' => 'integer',
        'activo' => 'boolean',
    ];

    /**
     * @brief Obtiene el criterio de evaluación al que pertenece esta capacidad.
     * 
     * @return BelongsTo Relación con el modelo CriterioEvaluacion.
     */
    public function perteneceACriterio(): BelongsTo
    {
        return $this->belongsTo(CriterioEvaluacion::class, 'criterio_id');
    }
}
