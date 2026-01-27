<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Notifications\Notification;

/**
 * @class Evaluacion
 * @brief Modelo que representa la evaluación final de un alumno.
 */
class Evaluacion extends Model
{
    use SoftDeletes;

    protected $table = 'evaluacions';

    /**
     * @brief Lógica al crear una evaluación: Notifica al alumno.
     */
    protected static function booted()
    {
        static::created(function ($evaluacion) {
            $alumno = $evaluacion->alumno;
            if ($alumno && $alumno->user) {
                Notification::make()
                    ->title('Nueva Evaluación Publicada')
                    ->body('Se ha publicado tu evaluación final con una nota de ' . $evaluacion->nota_final)
                    ->success()
                    ->icon('heroicon-o-academic-cap')
                    ->sendToDatabase($alumno->user);
            }
        });
    }

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
