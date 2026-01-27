<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @class Empresa
 * @brief Modelo que representa a una Empresa colaboradora.
 */
class Empresa extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'nombre',
        'cif',
        'direccion',
        'localidad',
        'provincia',
        'codigo_postal',
        'telefono',
        'email',
        'web',
        'persona_contacto',
        'sector',
        'activa',
        'fecha_creacion',
    ];

    /**
     * @brief Relación 1:1 con el modelo User.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function alumnos(): HasMany
    {
        return $this->hasMany(Alumno::class);
    }

    public function tutoresPracticas(): HasMany
    {
        return $this->hasMany(TutorPracticas::class);
    }
}
