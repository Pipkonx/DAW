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
    public function index(Request $request)
    {
        return view('profile.security', [
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
     * Devuelve el código OTP actual y el tiempo restante (para el temporizador).
     */
    public function getCurrentOtp(Request $request)
    {
        $request->validate(['secret' => 'required|string']);
        
        $secret = $request->secret;
        $period = 30; // Estándar por defecto
        $timestamp = time();
        $secondsRemaining = $period - ($timestamp % $period);
        
        return response()->json([
            'code' => Google2FA::getCurrentOtp($secret),
            'secondsRemaining' => $secondsRemaining,
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

            // Registrar la acción con enriquecimiento
            $this->logActivity($user, '2fa_enabled');

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

        // Registrar la acción con enriquecimiento
        $this->logActivity($user, '2fa_disabled');

        return back();
    }

    /**
     * Registra la actividad de seguridad con metadatos enriquecidos.
     */
    private function logActivity($user, $type)
    {
        $ip = request()->ip();
        $location = \Stevebauman\Location\Facades\Location::get($ip);
        $agent = new \Jenssegers\Agent\Agent();
        $agent->setUserAgent(request()->userAgent());
        
        $browser = $agent->browser();

        LoginActivity::create([
            'user_id' => $user->id,
            'ip_address' => $ip,
            'city' => $location ? $location->cityName : 'Local',
            'country' => $location ? $location->countryName : 'Reserved',
            'user_agent' => request()->userAgent(),
            'browser' => $browser,
            'browser_version' => $agent->version($browser),
            'os' => $agent->platform(),
            'device' => $agent->device(),
            'session_id' => request()->session()->getId(),
            'type' => $type
        ]);
    }
}
