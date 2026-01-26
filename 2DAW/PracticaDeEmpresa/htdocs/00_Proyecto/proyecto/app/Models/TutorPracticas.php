<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class TutorPracticas
 * @brief Modelo que representa a un Tutor de Prácticas.
 */
class TutorPracticas extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'empresa_id',
        'nombre',
        'apellidos',
        'dni',
        'email',
        'telefono',
        'cargo',
        'horario',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'tutor_practicas_id');
    }
}
