@extends('layouts.app')

@section('content')
<div class="py-5 bg-white min-vh-100">
    <div class="container" style="max-width: 800px;">
        <div class="text-center mb-5">
            <h1 class="fw-black text-dark display-4">Términos y Condiciones</h1>
            <p class="text-muted fw-bold small text-uppercase tracking-widest">Última actualización: Abril 2026</p>
        </div>

        <div class="content shadow-sm p-4 p-md-5 rounded-4 border bg-white" style="line-height: 1.8; color: #444;">
            <section class="mb-5">
                <h4 class="fw-black text-primary mb-3">1. Aceptación del Servicio</h4>
                <p>Al acceder y utilizar Pipkonx, usted acepta estar sujeto a estos Términos y Condiciones. Si no está de acuerdo con alguna parte de estos términos, no podrá utilizar nuestros servicios.</p>
            </section>

            <section class="mb-5">
                <h4 class="fw-black text-primary mb-3">2. Naturaleza del Servicio</h4>
                <p>Pipkonx es una plataforma de gestión patrimonial y análisis de datos financieros. La información proporcionada por el <strong>Analista IA</strong> tiene fines exclusivamente informativos y no constituye asesoramiento financiero profesional.</p>
            </section>

            <section class="mb-5">
                <h4 class="fw-black text-primary mb-3">3. Cuentas de Usuario</h4>
                <p>Usted es responsable de mantener la confidencialidad de su cuenta y contraseña. Pipkonx no se hace responsable de las pérdidas resultantes de un uso no autorizado de su cuenta.</p>
            </section>

            <div class="alert alert-info rounded-4 border-0 p-4 small">
                <i class="bi bi-info-circle-fill me-2"></i> Estos términos pueden ser modificados en cualquier momento para reflejar cambios legales o funcionales en la plataforma.
            </div>
        </div>
    </div>
</div>
@endsection
