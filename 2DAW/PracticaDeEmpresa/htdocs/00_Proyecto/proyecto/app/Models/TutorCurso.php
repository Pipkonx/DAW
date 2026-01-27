<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class TutorCurso
 * @brief Modelo que representa a un Tutor de Curso.
 */
class TutorCurso extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'nombre',
        'apellidos',
        'dni',
        'email',
        'telefono',
        'departamento',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
