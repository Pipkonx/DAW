<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class CriterioEvaluacion
 * @brief Modelo que representa un criterio general de evaluación.
 */
class CriterioEvaluacion extends Model
{
    use SoftDeletes;

    protected $table = 'criterio_evaluacions';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    /**
     * @brief Obtiene todas las capacidades asociadas a este criterio.
     * 
     * @return HasMany Relación con el modelo CapacidadEvaluacion.
     */
    public function obtenerCapacidades(): HasMany
    {
        return $this->hasMany(CapacidadEvaluacion::class, 'criterio_id');
    }
}
