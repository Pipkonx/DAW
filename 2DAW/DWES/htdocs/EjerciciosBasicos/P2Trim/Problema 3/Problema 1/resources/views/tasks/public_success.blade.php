@extends('layouts.public')

@section('content')
<div class="card p-5 text-center">
    <div class="mb-4">
        <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
    </div>
    <h3 class="fw-bold mb-3">¡Incidencia Registrada con Éxito!</h3>
    <p class="text-muted mb-4">
        Hemos recibido su solicitud correctamente. Un operario será asignado a la brevedad para resolver su incidencia.
    </p>
    <div class="d-grid gap-2 col-md-6 mx-auto">
        <a href="{{ route('tasks.public.create') }}" class="btn btn-outline-primary">
            Registrar otra incidencia
        </a>
    </div>
</div>
@endsection