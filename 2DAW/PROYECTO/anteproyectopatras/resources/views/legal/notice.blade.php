@extends('layouts.app')

@section('content')
<div class="py-5 bg-white min-vh-100">
    <div class="container" style="max-width: 800px;">
        <div class="text-center mb-5">
            <h1 class="fw-black text-dark display-4">Aviso Legal</h1>
            <p class="text-muted fw-bold small text-uppercase tracking-widest">Información mercantil y legal</p>
        </div>

        <div class="content shadow-sm p-4 p-md-5 rounded-4 border bg-white" style="line-height: 1.8; color: #444;">
            <section class="mb-5">
                <h4 class="fw-black text-primary mb-3">Identidad del Titular</h4>
                <p>En cumplimiento de la Ley 34/2002 de Servicios de la Sociedad de la Información y de Comercio Electrónico (LSSI-CE):</p>
                <ul class="list-unstyled fw-bold small">
                    <li class="mb-2"><i class="bi bi-dot me-2"></i> Denominación: Pipkonx Global S.L.</li>
                    <li class="mb-2"><i class="bi bi-dot me-2"></i> CIF: B00000000</li>
                    <li class="mb-2"><i class="bi bi-dot me-2"></i> Dirección: Tech Hub, Planta 5, Madrid</li>
                    <li class="mb-2"><i class="bi bi-dot me-2"></i> Email: legal@pipkonx.com</li>
                </ul>
            </section>

            <section class="mb-5">
                <h4 class="fw-black text-primary mb-3">Propiedad Intelectual</h4>
                <p>El nombre Pipkonx, sus algoritmos de análisis financiero y el diseño de la interfaz son propiedad exclusiva del titular. Queda prohibida su reproducción total o parcial sin autorización expresa.</p>
            </section>
        </div>
    </div>
</div>
@endsection
