<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Buscar usuario por google_id
            $user = User::where('google_id', $googleUser->id)->first();

            if ($user) {
                // Si existe, loguear
                Auth::login($user);
                return redirect()->intended('dashboard');
            }

            // Buscar usuario por email para vincular cuenta existente
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // Si existe por email pero no tiene google_id, vincular
                $user->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                ]);
                Auth::login($user);
                return redirect()->intended('dashboard');
            }

            // Si no existe, crear nuevo usuario
            $newUser = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'password' => null, // Usuario sin contraseña
                'email_verified_at' => now(), // Google ya verificó el email
            ]);

            Auth::login($newUser);
            return redirect()->intended('dashboard');

        } catch (\Exception $e) {
            // Manejar error (cancelación, etc.)
            return redirect()->route('login')->with('error', 'Error al iniciar sesión con Google: ' . $e->getMessage());
        }
    }
}
