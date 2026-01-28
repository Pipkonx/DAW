<?php

namespace App\Observers;

use App\Models\Practice;
use Resend\Laravel\Facades\Resend;
use Spatie\GoogleCalendar\Event;
use Illuminate\Support\Facades\Log;

class PracticeObserver
{
    /**
     * Handle the Practice "created" event.
     */
    public function created(Practice $practice): void
    {
        $this->syncWithGoogleCalendar($practice);
        $this->sendNotificationViaResend($practice, 'Nueva Práctica Asignada');
    }

    /**
     * Handle the Practice "updated" event.
     */
    public function updated(Practice $practice): void
    {
        $this->syncWithGoogleCalendar($practice);
        $this->sendNotificationViaResend($practice, 'Práctica Actualizada');
    }

    /**
     * Sincroniza la práctica con Google Calendar.
     */
    protected function syncWithGoogleCalendar(Practice $practice): void
    {
        $calendarId = config('google-calendar.calendar_id');
        
        if (empty($calendarId)) {
            Log::warning('Google Calendar ID no configurado. Saltando sincronización.');
            return;
        }

        try {
            $event = new Event;
            $event->name = $practice->title;
            $event->description = $practice->description;
            $event->startDateTime = $practice->starts_at;
            $event->endDateTime = $practice->ends_at;
            $event->save();
        } catch (\Exception $e) {
            Log::error('Error sincronizando con Google Calendar: ' . $e->getMessage());
        }
    }

    /**
     * Envía una notificación vía Resend.
     */
    protected function sendNotificationViaResend(Practice $practice, string $subject): void
    {
        try {
            $user = $practice->alumno?->user;
            if ($user && $user->email) {
                Resend::emails()->send([
                    'from' => 'onboarding@resend.dev',
                    'to' => $user->email,
                    'subject' => $subject,
                    'html' => "<strong>Hola {$user->name},</strong><p>Se ha registrado una actividad en tu calendario de prácticas: <strong>{$practice->title}</strong></p>",
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error enviando notificación via Resend: ' . $e->getMessage());
        }
    }
}
