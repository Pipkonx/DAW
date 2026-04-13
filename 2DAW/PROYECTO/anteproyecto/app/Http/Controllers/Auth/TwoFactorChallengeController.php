<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class TwoFactorChallengeController extends Controller
{
    /**
     * Muestra la pantalla del desafío 2FA.
     */
    public function create(Request $request)
    {
        if (!$request->session()->has('auth.2fa.id')) {
            return redirect()->route('login');
        }

        return Inertia::render('Auth/TwoFactorChallenge');
    }

    /**
     * Valida el código 2FA y completa el inicio de sesión.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        if (!$request->session()->has('auth.2fa.id')) {
            return redirect()->route('login');
        }

        $userId = $request->session()->get('auth.2fa.id');
        $user = User::findOrFail($userId);

        $valid = Google2FA::verifyKey($user->google2fa_secret, $request->code);

        if ($valid) {
            // Limpiar la sesión temporal
            $request->session()->forget('auth.2fa.id');

            // Autenticar al usuario
            Auth::login($user, $request->session()->get('auth.2fa.remember', false));

            // Regenerar sesión para el login final
            $request->session()->regenerate();

            // Marcar 2FA como verificado en esta sesión para el middleware si existe
            $request->session()->put('google2fa.verified', true);

            return redirect()->intended(route('dashboard', absolute: false));
        }

        return back()->withErrors(['code' => 'El código introducido es incorrecto o ha caducado.']);
    }
}
