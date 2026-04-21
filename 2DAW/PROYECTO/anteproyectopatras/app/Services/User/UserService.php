<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * UserService - Gestor de la identidad y ciclo de vida del usuario.
 * 
 * Centraliza las operaciones de modificación de datos personales y la 
 * lógica de eliminación de cuentas, garantizando la integridad de los datos.
 */
class UserService
{
    /**
     * Actualiza la información del perfil del usuario.
     */
    public function updateProfile(User $user, array $data): bool
    {
        $user->fill($data);

        // Si cambia el email, invalidar la verificación previa
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        return $user->save();
    }

    /**
     * Procesa la eliminación permanente de la cuenta del usuario.
     */
    public function deleteAccount(User $user): bool
    {
        Auth::logout();
        
        // El soft delete o force delete depende de la configuración del modelo User
        return $user->delete();
    }

    /**
     * Alterna el rol de administrador de un usuario (Prevención de auto-desanclaje).
     */
    public function toggleAdmin(User $admin, User $target): bool
    {
        if ($admin->id === $target->id) return false;
        return $target->update(['is_admin' => !$target->is_admin]);
    }

    /**
     * Concede o actualiza un plan de suscripción de forma manual.
     */
    public function updateSubscription(User $user, string $tier, ?int $days = 30): void
    {
        if ($tier === 'none') {
            \DB::table('subscriptions')->where('user_id', $user->id)->delete();
            return;
        }

        $priceId = config("services.stripe.price_{$tier}", "price_{$tier}_id");
        $endsAt = now()->addDays($days ?: 30);

        \DB::table('subscriptions')->updateOrInsert(
            ['user_id' => $user->id, 'type' => 'default'],
            [
                'stripe_id' => 'manual_' . uniqid(),
                'stripe_status' => 'active',
                'stripe_price' => $priceId,
                'quantity' => 1,
                'ends_at' => $endsAt,
                'updated_at' => now(),
                'created_at' => now() // Solo para nuevas inserciones
            ]
        );
    }

    /**
     * Obtiene el listado de usuarios bloqueados por el usuario actual.
     */
    public function getBlockedUsers(User $user)
    {
        return User::whereIn('id', function($query) use ($user) {
            $query->select('blocked_id')->from('blocks')->where('blocker_id', $user->id);
        })->get(['id', 'name', 'username', 'avatar']);
    }
}
