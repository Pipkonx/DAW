<?php

namespace App\Observers;

use App\Models\Incidencia;
use App\Services\MailtrapService;

class IncidenciaObserver
{
    protected $mailtrapService;

    public function __construct(MailtrapService $mailtrapService)
    {
        $this->mailtrapService = $mailtrapService;
    }

    /**
     * Handle the Incidencia "created" event.
     */
    public function created(Incidencia $incidencia): void
    {
        $alumno = $incidencia->alumno;
        $tutorCurso = $alumno->tutorCurso;

        if ($tutorCurso && $tutorCurso->email) {
            $subject = "Nueva Incidencia: " . $incidencia->tipo;
            $content = "Se ha registrado una nueva incidencia para el alumno " . $alumno->user->name . ".\n\n" .
                       "Tipo: " . $incidencia->tipo . "\n" .
                       "Fecha: " . $incidencia->fecha . "\n" .
                       "Descripción: " . $incidencia->descripcion;

            $this->mailtrapService->sendEmail(
                $tutorCurso->email,
                $subject,
                $content,
                'Incidencia'
            );
        }
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
