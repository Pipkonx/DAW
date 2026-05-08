<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class RedSocialController extends Controller
{
    /**
     * Redirige al usuario al proveedor (Google, Facebook, etc.)
     */
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Maneja la respuesta del proveedor
     */
    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Error al autenticar con ' . $provider);
        }

        // Buscamos si el usuario ya existe por su ID de proveedor o email
        $usuario = Usuario::where('provider_id', $socialUser->getId())
                    ->where('provider_name', $provider)
                    ->first();

        if (!$usuario) {
            $usuario = Usuario::where('email', $socialUser->getEmail())->first();

            if ($usuario) {
                // Si existe por email, lo vinculamos
                $usuario->update([
                    'provider_id' => $socialUser->getId(),
                    'provider_name' => $provider,
                ]);
            } else {
                // Si no existe, lo creamos
                $usuario = Usuario::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                    'email' => $socialUser->getEmail(),
                    'provider_id' => $socialUser->getId(),
                    'provider_name' => $provider,
                    'password' => null, // No necesita password si entra por Socialite
                ]);
            }
        }

        Auth::login($usuario);

        return redirect('/cuotas');
    }
}
