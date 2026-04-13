<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\LoginActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use PragmaRX\Google2FALaravel\Facade as Google2FA;

class SecurityController extends Controller
{
    /**
     * Muestra el historial de actividad de seguridad y ajustes de 2FA.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Profile/Security', [
            'activities' => LoginActivity::where('user_id', Auth::id())
                ->latest()
                ->limit(20)
                ->get(),
            'currentSessionId' => $request->session()->getId(),
            'twoFactorEnabled' => !is_null(Auth::user()->google2fa_secret),
        ]);
    }

    /**
     * Inicia el proceso de configuración de 2FA (Genera QR).
     */
    public function setup2fa(Request $request)
    {
        $user = Auth::user();
        
        // Generar un nuevo secreto si no existe
        $secret = Google2FA::generateSecretKey();
        
        // Generar URL del código QR con el nombre de la app personalizado (fintechPro)
        $qrCodeUrl = Google2FA::getQRCodeInline(
            'fintechPro',
            $user->email,
            $secret
        );

        return response()->json([
            'secret' => $secret,
            'qrCodeUrl' => $qrCodeUrl,
        ]);
    }

    /**
     * Valida y activa el 2FA tras verificar el primer código.
     */
    public function activate2fa(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'secret' => 'required|string',
        ]);

        $valid = Google2FA::verifyKey($request->secret, $request->code);

        if ($valid) {
            $user = Auth::user();
            $user->google2fa_secret = $request->secret;
            $user->two_factor_enabled = true; // Mantener retrocompatibilidad de flag
            $user->save();

            // Registrar la acción
            LoginActivity::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => $request->session()->getId(),
                'type' => '2fa_enabled'
            ]);

            return back()->with('message', '2FA activado correctamente.');
        }

        return back()->withErrors(['code' => 'El código de verificación no es válido.']);
    }

    /**
     * Desactiva el 2FA.
     */
    public function disable2fa(Request $request)
    {
        $user = Auth::user();
        $user->google2fa_secret = null;
        $user->two_factor_enabled = false;
        $user->save();

        LoginActivity::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->session()->getId(),
            'type' => '2fa_disabled'
        ]);

        return back();
    }
}
