@extends('layouts.app')

@section('title', 'Simulación PayPal - Nosecaen S.L.')

@section('content')
<div class="login-card text-center">
    <div class="mb-3">
        <img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_111x69.jpg" alt="PayPal" class="img-fluid" style="max-height: 40px;">
    </div>
    
    <h3 class="h5 fw-bold mb-1">Simulación de Pago</h3>
    <p class="text-muted small mb-4">{{ $cuota->concept }}</p>
    
    <div class="bg-light p-4 rounded-3 border mb-4">
        <p class="text-uppercase small text-secondary mb-1 fw-semibold">Total a pagar</p>
        <p class="h2 fw-bold text-dark mb-0">{{ number_format($cuota->amount, 2) }} {{ $cuota->currency }}</p>
    </div>

    <div id="paypal-button-container" class="mt-4"></div>
    
    <div class="mt-4 border-top pt-3">
        <a href="{{ route('cuotas.index') }}" class="text-decoration-none text-muted small">Cancelar y volver</a>
    </div>
</div>

<script src="https://www.paypal.com/sdk/js?client-id=test&currency={{ $cuota->currency }}"></script>

<script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '{{ $cuota->amount }}'
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                // Al aprobar el pago, redirigimos a nuestra ruta de éxito
                window.location.href = "{{ route('pago.exito') }}";
            });
        }
    }).render('#paypal-button-container');
</script>
@endsection
