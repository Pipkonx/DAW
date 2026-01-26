<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluacion extends Model
{
    use SoftDeletes;

    protected $table = 'evaluacions';

    protected $fillable = [
        'alumno_id',
        'tutor_practicas_id',
        'nota_final',
        'observaciones',
    ];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    public function tutorPracticas(): BelongsTo
    {
        return $this->belongsTo(TutorPracticas::class, 'tutor_practicas_id');
    }

    public function detalles(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EvaluacionDetalle::class);
    }
}
