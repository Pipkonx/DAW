<?php

namespace App\Observers;

use App\Models\Incidencia;
use App\Mail\IncidenciaRegistrada;
use Illuminate\Support\Facades\Mail;

class IncidenciaObserver
{
    /**
     * Handle the Incidencia "created" event.
     */
    public function created(Incidencia $incidencia): void
    {
        $alumno = $incidencia->alumno;
        if (!$alumno) return;

        // Notificar al Tutor de Curso
        $tutorCurso = $alumno->tutorCurso;
        if ($tutorCurso && $tutorCurso->email) {
            Mail::to($tutorCurso->email)->send(new IncidenciaRegistrada($incidencia));
        }

        // También podrías notificar al Admin o al creador si es necesario
    }

    /**
     * Handle the Incidencia "updated" event.
     */
    public function updated(Incidencia $incidencia): void
    {
        //
    }

    /**
     * Handle the Incidencia "deleted" event.
     */
    public function deleted(Incidencia $incidencia): void
    {
        //
    }

    /**
     * Handle the Incidencia "restored" event.
     */
    public function restored(Incidencia $incidencia): void
    {
        //
    }

    /**
     * Handle the Incidencia "force deleted" event.
     */
    public function forceDeleted(Incidencia $incidencia): void
    {
        //
    }
}
