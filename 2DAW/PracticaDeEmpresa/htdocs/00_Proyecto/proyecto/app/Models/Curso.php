<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Curso extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'duracion',
        'fecha_inicio',
        'fecha_fin',
        'tutor_curso_id',
        'activo',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    /**
     * @brief Relación con el Tutor de Curso.
     * @return BelongsTo
     */
    public function tutorCurso(): BelongsTo
    {
        return $this->belongsTo(TutorCurso::class, 'tutor_curso_id');
    }

    public function alumnos(): HasMany
    {
        return $this->hasMany(Alumno::class);
    }
}
