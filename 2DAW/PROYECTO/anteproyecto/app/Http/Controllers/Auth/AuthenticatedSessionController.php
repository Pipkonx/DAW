<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

use App\Models\LoginActivity;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Si el usuario tiene habilitado el 2FA real (tiene un secreto guardado)
        if ($user->google2fa_secret) {
            $userId = $user->id;
            $remember = $request->boolean('remember');
            
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $request->session()->put('auth.2fa.id', $userId);
            $request->session()->put('auth.2fa.remember', $remember);

            return redirect()->route('login.2fa');
        }

        $request->session()->regenerate();

        // --- ENRIQUECIMIENTO DE ACTIVIDAD ---
        $ip = $request->ip();
        $agent = new \Jenssegers\Agent\Agent();
        $agent->setUserAgent($request->userAgent());
        $browser = $agent->browser();

        // Proteger la llamada externa de geolocalización (puede fallar o hacer timeout)
        try {
            $location = \Stevebauman\Location\Facades\Location::get($ip);
            $city    = $location ? $location->cityName    : 'Local';
            $country = $location ? $location->countryName : 'Reserved';
        } catch (\Exception $e) {
            $city    = 'Local';
            $country = 'Reserved';
        }

        LoginActivity::create([
            'user_id'         => Auth::id(),
            'ip_address'      => $ip,
            'city'            => $city,
            'country'         => $country,
            'user_agent'      => $request->userAgent(),
            'browser'         => $browser,
            'browser_version' => $agent->version($browser),
            'os'              => $agent->platform(),
            'device'          => $agent->device(),
            'session_id'      => $request->session()->getId(),
            'type'            => 'login'
        ]);
        // ------------------------------------

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
