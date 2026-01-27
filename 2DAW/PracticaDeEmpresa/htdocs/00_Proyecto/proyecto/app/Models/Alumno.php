<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class Alumno
 * @brief Modelo que representa a un Alumno en el sistema.
 */
class Alumno extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'curso_id',
        'empresa_id',
        'tutor_curso_id',
        'tutor_practicas_id',
        'nombre',
        'apellidos',
        'dni',
        'fecha_nacimiento',
        'email',
        'telefono',
        'duracion_practicas',
        'horario',
        'fecha_inicio',
        'fecha_fin',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function curso(): BelongsTo
    {
        return $this->belongsTo(Curso::class);
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function tutorCurso(): BelongsTo
    {
        return $this->belongsTo(TutorCurso::class);
    }

    public function tutorPracticas(): BelongsTo
    {
        return $this->belongsTo(TutorPracticas::class);
    }

    public function observacionesDiarias(): HasMany
    {
        return $this->hasMany(ObservacionDiaria::class);
    }

    public function incidencias(): HasMany
    {
        return $this->hasMany(Incidencia::class);
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class);
    }
}
