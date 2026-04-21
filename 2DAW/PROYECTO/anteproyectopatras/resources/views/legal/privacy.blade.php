@extends('layouts.app')

@section('content')
<div class="py-5 bg-white min-vh-100">
    <div class="container" style="max-width: 800px;">
        <div class="text-center mb-5">
            <h1 class="fw-black text-dark display-4">Política de Privacidad</h1>
            <p class="text-muted fw-bold small text-uppercase tracking-widest">Compromiso con la seguridad de tus datos</p>
        </div>

        <div class="content shadow-sm p-4 p-md-5 rounded-4 border bg-white" style="line-height: 1.8; color: #444;">
            <section class="mb-5">
                <h4 class="fw-black text-primary mb-3">1. Recopilación de Datos</h4>
                <p>Pipkonx recopila información necesaria para el seguimiento de sus inversiones, incluyendo tickers de activos, cantidades y precios. No almacenamos credenciales de acceso a bancos externos si usted utiliza la carga manual por CSV.</p>
            </section>

            <section class="mb-5">
                <h4 class="fw-black text-primary mb-3">2. Uso de la Inteligencia Artificial</h4>
                <p>Sus datos patrimoniales son anonimizados antes de ser procesados por nuestro motor de IA para generar informes estratégicos. No vendemos sus datos financieros a terceros.</p>
            </section>

            <section class="mb-5">
                <h4 class="fw-black text-primary mb-3">3. Seguridad</h4>
                <p>Implementamos medidas de seguridad avanzadas, incluyendo cifrado SSL y autenticación de dos factores (2FA), para proteger su información financiera.</p>
            </section>

            <div class="card bg-light border-0 rounded-4 p-4 mt-5">
                <div class="d-flex align-items-center">
                    <i class="bi bi-shield-check fs-1 text-primary me-3"></i>
                    <div>
                        <h6 class="fw-bold mb-1">Protección de Datos GDRP</h6>
                        <p class="small text-muted mb-0">Cumplimos con los estándares europeos de protección de privacidad.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
