<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EvaluacionTutor extends Model
{
    use SoftDeletes;

    protected $table = 'evaluacion_tutors';

    protected $fillable = [
        'alumno_id',
        'tutor_curso_id',
        'nota_final',
        'observaciones_finales',
    ];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    public function tutorCurso(): BelongsTo
    {
        return $this->belongsTo(TutorCurso::class);
    }
}
