<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Notifications\Notification;
use App\Models\User;

class Incidencia extends Model
{
    use SoftDeletes;

    protected static function booted()
    {
        static::created(function ($incidencia) {
            $alumno = $incidencia->alumno;
            $nombreAlumno = $alumno->nombre . ' ' . $alumno->apellidos;

            // Notificar a todos los administradores
            $admins = User::role('admin')->get();
            
            // Notificar al tutor del curso asignado al alumno
            $tutorCurso = $alumno->tutorCurso;

            $recipients = $admins;
            if ($tutorCurso && $tutorCurso->user) {
                $recipients->push($tutorCurso->user);
            }

            Notification::make()
                ->title('Nueva incidencia de ' . $nombreAlumno)
                ->body('Se ha registrado una nueva incidencia de tipo: ' . $incidencia->tipo)
                ->danger()
                ->icon('heroicon-o-exclamation-triangle')
                ->sendToDatabase($recipients);
        });
    }

    protected $fillable = [
        'alumno_id',
        'tutor_practicas_id',
        'fecha',
        'tipo',
        'descripcion',
        'resolucion',
        'estado',
        'fecha_resolucion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_resolucion' => 'datetime',
    ];

    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    public function tutorPracticas(): BelongsTo
    {
        return $this->belongsTo(TutorPracticas::class);
    }
}
