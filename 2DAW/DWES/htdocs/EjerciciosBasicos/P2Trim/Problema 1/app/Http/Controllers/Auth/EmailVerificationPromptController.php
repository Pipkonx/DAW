<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Muestra el aviso para verificar el correo electrónico.
     * 
     * EXPLICACIÓN: __invoke
     * Este nombre de función especial permite llamar a esta clase directamente,
     * sin especificar un método. Se usa para controladores que solo hacen UNA cosa.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        // Si ya está verificado, lo mandamos al panel. Si no, a la vista de aviso.
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email');
    }
}
