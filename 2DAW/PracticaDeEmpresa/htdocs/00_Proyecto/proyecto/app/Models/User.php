<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;

/**
 * @class User
 * @brief Modelo que representa a un usuario del sistema.
 * 
 * Centraliza la autenticación y las relaciones con los diferentes perfiles
 * de usuario. Utiliza el trait Notifiable para la gestión de notificaciones.
 */
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements HasAvatar, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    /**
     * @brief Trait Notifiable
     * Esencial para que Filament pueda enviar notificaciones a la base de datos
     * y mostrarlas en el panel de usuario.
     */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * @brief Determina si el usuario puede acceder al panel de Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true; // O la lógica que prefieras
    }

    /**
     * @brief Obtiene la URL del avatar para Filament.
     * 
     * @return string|null
     */
    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->avatar_url) {
            // Si la ruta ya es una URL completa (ej. ui-avatars), la devolvemos
            if (filter_var($this->avatar_url, FILTER_VALIDATE_URL)) {
                return $this->avatar_url;
            }
            
            // Si el archivo existe en el disco público, devolvemos su URL configurada
            if (Storage::disk('public')->exists($this->avatar_url)) {
                return Storage::disk('public')->url($this->avatar_url);
            }
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=FFFFFF&background=111827';
    }

    /**
     * @brief Obtiene el nombre del primer rol asignado.
     * 
     * @return string Nombre del rol o 'Sin Rol'.
     */
    public function getRoleNameAttribute(): string
    {
        return $this->getRoleNames()->first() ?? 'Sin Rol';
    }

    /**
     * @brief Comprueba si el usuario tiene el rol de administrador.
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * @brief Comprueba si el usuario tiene el rol de tutor de curso.
     * @return bool
     */
    public function isTutorCurso(): bool
    {
        return $this->hasRole('tutor_curso');
    }

    /**
     * @brief Comprueba si el usuario tiene el rol de tutor de prácticas.
     * @return bool
     */
    public function isTutorPracticas(): bool
    {
        return $this->hasRole('tutor_practicas');
    }

    /**
     * @brief Comprueba si el usuario tiene el rol de tutor de prácticas (alias para compatibilidad).
     * @return bool
     */
    public function isTutorEmpresa(): bool
    {
        return $this->isTutorPracticas();
    }

    /**
     * @brief Comprueba si el usuario tiene el rol de alumno.
     * @return bool
     */
    public function isAlumno(): bool
    {
        return $this->hasRole('alumno');
    }

    /**
     * @brief Define la relación 1:1 con el perfil de Alumno.
     * 
     * @return HasOne
     */
    public function alumno(): HasOne
    {
        return $this->hasOne(Alumno::class);
    }

    /**
     * @brief Define la relación 1:1 con el perfil de Empresa.
     * 
     * @return HasOne
     */
    public function empresa(): HasOne
    {
        return $this->hasOne(Empresa::class);
    }

    /**
     * @brief Define la relación 1:1 con el perfil de Tutor de Curso.
     * 
     * @return HasOne
     */
    public function perfilTutorCurso(): HasOne
    {
        return $this->hasOne(TutorCurso::class);
    }

    /**
     * @brief Define la relación 1:1 con el perfil de Tutor de Prácticas.
     * 
     * @return HasOne
     */
    public function perfilTutorPracticas(): HasOne
    {
        return $this->hasOne(TutorPracticas::class);
    }

    public function messagesSent()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function messagesReceived()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * @brief Obtiene el estado de conexión del usuario.
     */
    public function isOnline(): bool
    {
        return \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', $this->id)
            ->where('last_activity', '>=', now()->subMinutes(5)->getTimestamp())
            ->exists();
    }

    /**
     * @brief Obtiene la última vez que el usuario estuvo activo.
     */
    public function lastSeen(): ?string
    {
        $session = \Illuminate\Support\Facades\DB::table('sessions')
            ->where('user_id', $this->id)
            ->orderBy('last_activity', 'desc')
            ->first();

        if (!$session) {
            return null;
        }

        return \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans();
    }

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'reference_id',
        'avatar_url',
        'google_id',
    ];

    /**
     * Los atributos que deben ocultarse para la serialización.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Obtiene los atributos que deben ser casteados.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
