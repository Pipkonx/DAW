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
                
                // --- INTEGRACIÓN 2FA ---
                if ($user->google2fa_secret) {
                    $userId = $user->id;
                    Auth::guard('web')->logout();
                    session()->invalidate();
                    session()->regenerateToken();
                    session()->put('auth.2fa.id', $userId);
                    return redirect()->route('login.2fa');
                }
                // -----------------------

                // Registrar actividad de seguridad
                $this->logActivity($user);

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

                // --- INTEGRACIÓN 2FA ---
                if ($user->google2fa_secret) {
                    $userId = $user->id;
                    Auth::guard('web')->logout();
                    session()->invalidate();
                    session()->regenerateToken();
                    session()->put('auth.2fa.id', $userId);
                    return redirect()->route('login.2fa');
                }
                // -----------------------

                // Registrar actividad de seguridad
                $this->logActivity($user);

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

            // Registrar actividad de seguridad
            $this->logActivity($newUser);

            return redirect()->intended('dashboard');

        } catch (\Exception $e) {
            // Manejar error (cancelación, etc.)
            return redirect()->route('login')->with('error', 'Error al iniciar sesión con Google: ' . $e->getMessage());
        }
    }

    /**
     * Registra la actividad de inicio de sesión con metadatos enriquecidos.
     */
    protected function logActivity($user)
    {
        $ip = request()->ip();
        $location = \Stevebauman\Location\Facades\Location::get($ip);
        $agent = new \Jenssegers\Agent\Agent();
        $agent->setUserAgent(request()->userAgent());
        
        $browser = $agent->browser();

        \App\Models\LoginActivity::create([
            'user_id' => $user->id,
            'ip_address' => $ip,
            'city' => $location ? $location->cityName : 'Local',
            'country' => $location ? $location->countryName : 'Reserved',
            'user_agent' => request()->userAgent(),
            'browser' => $browser,
            'browser_version' => $agent->version($browser),
            'os' => $agent->platform(),
            'device' => $agent->device(),
            'session_id' => session()->getId(),
            'type' => 'login'
        ]);
    }
}
