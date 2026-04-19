<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialController extends Controller
{
    // Redirige al usuario a Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Recibe la respuesta de Google
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Buscamos si el usuario ya existe por su email
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // Si no existe, lo creamos (como operario por defecto)
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make(Str::random(24)),
                    'role' => 'operator',
                    'is_active' => true,
                    'dni' => 'VAR-' . Str::random(5), // DNI temporal
                ]);
            }

            Auth::login($user);

            return redirect()->intended('dashboard');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Error al entrar con Google');
        }
    }
}
