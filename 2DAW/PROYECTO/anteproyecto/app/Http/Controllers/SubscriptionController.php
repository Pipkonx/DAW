<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

/**
 * Controlador para gestionar los planes de suscripción y pagos con Stripe.
 */
class SubscriptionController extends Controller
{
    /**
     * Muestra la página de selección de planes.
     */
    public function index()
    {
        $plans = [
            [
                'id' => config('services.stripe.price_basic'), 
                'name' => 'Plan Básico',
                'description' => 'Ideal para empezar. Sin anuncios y funciones esenciales de seguimiento.',
                'price' => '4.99',
                'features' => ['Sin anuncios', 'Métricas esenciales', 'Historial estándar', 'Soporte por email'],
                'color' => 'blue'
            ],
            [
                'id' => config('services.stripe.price_pro'), 
                'name' => 'Plan Pro',
                'description' => 'Para inversores serios. Desbloquea el poder de la Inteligencia Artificial.',
                'price' => '14.99',
                'features' => ['Sin anuncios', 'Análisis Avanzado (AI) de Cartera', 'Alertas en tiempo real', 'Soporte prioritario'],
                'color' => 'indigo',
                'popular' => true
            ],
            [
                'id' => config('services.stripe.price_premium'), 
                'name' => 'Plan Premium',
                'description' => 'La experiencia completa. Acceso total y reportes IA ilimitados.',
                'price' => '24.99',
                'features' => ['Sin anuncios', 'Todo lo del Plan Pro', 'Informes IA Ilimitados', 'VIP Support 24/7'],
                'color' => 'purple'
            ],
        ];

        return Inertia::render('Plans/Index', [
            'plans' => $plans,
            'intent' => Auth::user()->createSetupIntent(), // Necesario para Stripe Elements
        ]);
    }

    /**
     * Maneja el proceso de suscripción.
     */
    public function subscribe(Request $request)
    {
        $user = Auth::user();
        $planId = $request->plan_id;

        try {
            // Creamos una sesión de Checkout externa en Stripe
            $checkout = $user->newSubscription('default', $planId)
                ->checkout([
                    'success_url' => route('dashboard'),
                    'cancel_url' => route('subscription.index'),
                ]);

            return Inertia::location($checkout->url);
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear la sesión de pago: ' . $e->getMessage());
        }
    }
}
