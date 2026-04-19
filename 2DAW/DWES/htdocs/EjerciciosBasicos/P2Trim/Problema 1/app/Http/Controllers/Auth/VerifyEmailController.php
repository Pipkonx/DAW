<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Marca la dirección de correo del usuario como verificada.
     * 
     * EXPLICACIÓN: __invoke
     * Este nombre especial permite que Laravel use esta clase directamente 
     * como si fuera una función, sin tener que decirle qué método ejecutar.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // Si el usuario ya estaba verificado, lo mandamos al panel
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        // Si lo marcamos como verificado ahora, lanzamos un evento de "Verificado"
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // Al terminar, lo mandamos al panel con un mensaje de éxito
        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
