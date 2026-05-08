@extends('layouts.app')

@section('title', 'Acceso - Nosecaen S.L.')

@section('content')
<div class="contenedor-auth">
    <div class="tarjeta-auth">
        <h2>Acceso</h2>
        
        @if ($errors->any())
            <div class="alert alert-danger py-2 small">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small">Email</label>
                <input type="email" name="email" required class="form-control">
            </div>
            <div class="mb-4">
                <label class="form-label small">Contraseña</label>
                <input type="password" name="password" required class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary w-100 fw-bold">Entrar</button>
        </form>

        <div class="mt-4 border-top pt-4 text-center">
            <p class="small text-muted mb-2">O entrar con</p>
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('social.redirect', 'google') }}" class="btn btn-outline-danger btn-sm">Google</a>
                <a href="{{ route('social.redirect', 'github') }}" class="btn btn-outline-dark btn-sm">GitHub</a>
            </div>
        </div>

        <p class="mt-4 text-center small mb-0">
            ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
        </p>
    </div>
</div>
@endsection
