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

    <div id="payment-area">
        <button onclick="pay()" id="btn-pay" class="btn btn-warning w-100 py-3 rounded-pill fw-bold shadow-sm border-0" style="background-color: #ffc439; color: #003087;">
            Pagar ahora con PayPal
        </button>
        
        <div id="msg-processing" class="d-none mt-3">
            <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
            <span class="ms-2 text-primary fw-bold">Procesando pago seguro...</span>
        </div>
    </div>
    
    <div class="mt-4 border-top pt-3">
        <a href="{{ route('cuotas.index') }}" class="text-decoration-none text-muted small">Cancelar y volver</a>
    </div>
</div>

<script>
    function pay() {
        const btn = document.getElementById('btn-pay');
        const msg = document.getElementById('msg-processing');
        
        btn.classList.add('disabled');
        btn.innerHTML = 'Conectando...';
        
        setTimeout(() => {
            btn.classList.add('d-none');
            msg.classList.remove('d-none');
            
            setTimeout(() => {
                window.location.href = "{{ route('pago.exito') }}";
            }, 1500);
        }, 800);
    }
</script>
@endsection
