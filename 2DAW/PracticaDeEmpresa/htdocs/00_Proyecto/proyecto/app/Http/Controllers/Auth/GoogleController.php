<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Redirige al usuario a la página de autenticación de Google.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['https://www.googleapis.com/auth/calendar.events'])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    /**
     * Maneja el callback de Google.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Buscar el usuario solo por email, no crear si no existe
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                return redirect('/admin/login')->with('error', 'No se ha encontrado ninguna cuenta con este correo electrónico.');
            }

            // Actualizar el google_id, avatar y tokens
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar_url' => $user->avatar_url ?: $googleUser->getAvatar(),
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken ?: $user->google_refresh_token,
            ]);

            Auth::login($user);

            return redirect()->intended('/admin');
        } catch (Exception $e) {
            return redirect('/admin/login')->with('error', 'Error al iniciar sesión con Google: ' . $e->getMessage());
        }
    }
}
