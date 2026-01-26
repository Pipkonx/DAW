<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ObservacionDiaria extends Model
{
    use SoftDeletes;

    protected $table = 'observacion_diarias';

    protected $fillable = [
        'alumno_id',
        'fecha',
        'actividad',
        'horas',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }
}
