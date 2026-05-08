@extends('layouts.app')

@section('title', 'Registro - Nosecaen S.L.')

@section('content')
<div class="contenedor-auth">
    <div class="tarjeta-auth">
        <h2>Registro</h2>
        
        @if ($errors->any())
            <div class="alert alert-danger py-2 small">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small">Nombre</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label small">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label small">Contraseña</label>
                <input type="password" name="password" required class="form-control">
            </div>
            <div class="mb-4">
                <label class="form-label small">Repetir Contraseña</label>
                <input type="password" name="password_confirmation" required class="form-control">
            </div>
            
            <button type="submit" class="btn btn-success w-100 fw-bold">Registrar</button>
        </form>

        <p class="mt-4 text-center small mb-0">
            ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a>
        </p>
    </div>
</div>
@endsection
