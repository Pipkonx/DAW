<?php

namespace App\Observers;

use App\Models\Practice;
use App\Models\User;
use App\Models\Alumno;
use App\Models\PracticeGoogleEvent;
use App\Mail\PracticeNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;

class PracticeObserver
{
    /**
     * Handle the Practice "created" event.
     */
    public function created(Practice $practice): void
    {
        $this->syncWithGoogleCalendar($practice);
        $this->sendEmailNotifications($practice, 'creada');
    }

    /**
     * Handle the Practice "updated" event.
     */
    public function updated(Practice $practice): void
    {
        // Si la práctica acaba de ser creada, el evento created ya se encargó de las notificaciones
        // Esto evita duplicados cuando Filament realiza actualizaciones inmediatamente después de la creación
        if ($practice->wasRecentlyCreated) {
            return;
        }

        $this->syncWithGoogleCalendar($practice);
        $this->sendEmailNotifications($practice, 'actualizada');
    }

    /**
     * Handle the Practice "deleted" event.
     */
    public function deleted(Practice $practice): void
    {
        $this->removeFromGoogleCalendar($practice);
        $this->sendEmailNotifications($practice, 'eliminada');
    }

    /**
     * Sincroniza la práctica con Google Calendar de todos los usuarios afectados.
     */
    protected function syncWithGoogleCalendar(Practice $practice): void
    {
        // 1. Identificar usuarios actuales que deben tener el evento
        $targetUsers = $this->getTargetUsers($practice);
        $targetUserIds = $targetUsers->pluck('id')->toArray();

        // 2. Identificar usuarios que YA tienen un evento pero ya no deberían tenerlo (des-asignación)
        $obsoleteEvents = PracticeGoogleEvent::where('practice_id', $practice->id)
            ->whereNotIn('user_id', $targetUserIds)
            ->get();

        foreach ($obsoleteEvents as $obsoleteRecord) {
            $this->removeSpecificGoogleEvent($obsoleteRecord);
        }

        // 3. Sincronizar (crear/actualizar) para los usuarios actuales
        if ($targetUsers->isEmpty()) {
            Log::info('No se encontraron usuarios para sincronizar la práctica ' . $practice->id);
            return;
        }

        foreach ($targetUsers as $user) {
            $this->syncUserCalendar($practice, $user);
        }
    }

    /**
     * Elimina un evento específico de Google de un registro de seguimiento.
     */
    protected function removeSpecificGoogleEvent(PracticeGoogleEvent $record): void
    {
        $user = $record->user;
        if ($user && $user->google_token) {
            try {
                $client = new Client();
                $client->setClientId(config('services.google.client_id'));
                $client->setClientSecret(config('services.google.client_secret'));
                $client->setAccessToken($user->google_token);

                if ($client->isAccessTokenExpired() && $user->google_refresh_token) {
                    $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                }

                $service = new Calendar($client);
                $service->events->delete('primary', $record->google_event_id);
                Log::info("Evento des-asignado y eliminado para {$user->email}");
            } catch (\Exception $e) {
                Log::error("Error eliminando evento des-asignado para {$user->email}: " . $e->getMessage());
            }
        }
        $record->delete();
    }

    /**
     * Obtiene todos los usuarios que deben tener este evento en su calendario.
     */
    protected function getTargetUsers(Practice $practice): \Illuminate\Support\Collection
    {
        $users = collect();

        // 1. El alumno específico
        if ($practice->alumno_id) {
            $alumno = Alumno::find($practice->alumno_id);
            if ($alumno && $alumno->user) {
                $users->push($alumno->user);
            }
        } 
        
        // 2. Todos los alumnos de un curso
        if ($practice->curso_id) {
            $alumnoUsers = User::whereHas('alumno', function($query) use ($practice) {
                $query->where('curso_id', $practice->curso_id);
            })->get();
            $users = $users->merge($alumnoUsers);
        }

        // 3. Todos los usuarios con un rol específico
        if ($practice->target_role) {
            $roleUsers = User::role($practice->target_role)->get();
            $users = $users->merge($roleUsers);
        }

        // 4. El creador (siempre lo incluimos para que él también lo vea)
        if ($practice->user_id) {
            $creator = User::find($practice->user_id);
            if ($creator) {
                $users->push($creator);
            }
        }

        $foundUsers = $users->unique('id')->filter();
        Log::info('Usuarios destino encontrados para práctica ' . $practice->id . ': ' . $foundUsers->pluck('email')->implode(', '));

        return $foundUsers;
    }

    /**
     * Sincroniza el calendario de un usuario específico.
     */
    protected function syncUserCalendar(Practice $practice, User $user): void
    {
        if (!$user->google_token) {
            Log::info("Sincronización saltada para {$user->email}: Sin token de Google.");
            return;
        }

        try {
            $client = new Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setAccessToken($user->google_token);

            if ($client->isAccessTokenExpired()) {
                if ($user->google_refresh_token) {
                    $newToken = $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                    if (!isset($newToken['error'])) {
                        $user->update(['google_token' => $client->getAccessToken()['access_token']]);
                    } else {
                        Log::error("Error refrescando token para {$user->email}: " . json_encode($newToken));
                        return;
                    }
                } else {
                    return;
                }
            }

            $service = new Calendar($client);
            $eventData = [
                'summary' => $practice->title,
                'description' => $practice->description,
                'start' => new EventDateTime([
                    'dateTime' => $practice->starts_at->toRfc3339String(),
                    'timeZone' => config('app.timezone'),
                ]),
                'end' => new EventDateTime([
                    'dateTime' => ($practice->ends_at ?? $practice->starts_at->addHour())->toRfc3339String(),
                    'timeZone' => config('app.timezone'),
                ]),
            ];

            $googleEventRecord = PracticeGoogleEvent::where('practice_id', $practice->id)
                ->where('user_id', $user->id)
                ->first();

            if ($googleEventRecord) {
                try {
                    $event = $service->events->get('primary', $googleEventRecord->google_event_id);
                    $event->setSummary($eventData['summary']);
                    $event->setDescription($eventData['description']);
                    $event->setStart($eventData['start']);
                    $event->setEnd($eventData['end']);
                    $service->events->update('primary', $googleEventRecord->google_event_id, $event);
                    Log::info("Evento actualizado para {$user->email}");
                } catch (\Exception $e) {
                    // Si falló (ej. borrado manual en Google), crear de nuevo
                    $event = new Event($eventData);
                    $newEvent = $service->events->insert('primary', $event);
                    $googleEventRecord->update(['google_event_id' => $newEvent->getId()]);
                }
            } else {
                $event = new Event($eventData);
                $newEvent = $service->events->insert('primary', $event);
                PracticeGoogleEvent::create([
                    'practice_id' => $practice->id,
                    'user_id' => $user->id,
                    'google_event_id' => $newEvent->getId(),
                ]);
                Log::info("Evento creado para {$user->email}");
            }
        } catch (\Exception $e) {
            Log::error("Error sincronizando calendario de {$user->email}: " . $e->getMessage());
        }
    }

    /**
     * Elimina el evento de Google Calendar de todos los usuarios afectados.
     */
    protected function removeFromGoogleCalendar(Practice $practice): void
    {
        $googleEvents = PracticeGoogleEvent::where('practice_id', $practice->id)->get();

        foreach ($googleEvents as $record) {
            $user = $record->user;
            if (!$user || !$user->google_token) continue;

            try {
                $client = new Client();
                $client->setClientId(config('services.google.client_id'));
                $client->setClientSecret(config('services.google.client_secret'));
                $client->setAccessToken($user->google_token);

                if ($client->isAccessTokenExpired() && $user->google_refresh_token) {
                    $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);
                }

                $service = new Calendar($client);
                $service->events->delete('primary', $record->google_event_id);
                Log::info("Evento eliminado para {$user->email}");
            } catch (\Exception $e) {
                Log::error("Error eliminando evento para {$user->email}: " . $e->getMessage());
            }
            $record->delete();
        }
    }

    /**
     * Envía notificaciones por email a los usuarios afectados.
     */
    protected function sendEmailNotifications(Practice $practice, string $type): void
    {
        try {
            $targetUsers = $this->getTargetUsers($practice);
            
            foreach ($targetUsers as $user) {
                if ($user->email) {
                    Mail::to($user->email)->send(new PracticeNotification($practice, $type));
                }
            }
        } catch (\Exception $e) {
            Log::error('Error enviando notificaciones de práctica: ' . $e->getMessage());
        }
    }
}
