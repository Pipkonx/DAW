@extends('layouts.app')

@section('content')
    <!-- Página de Panel de Control: muestra datos del usuario y acciones rápidas -->
    <div>
        <div>Panel de control</div>

        <div>
            <!-- Información principal del usuario -->
            <p><strong>Usuario:</strong> {{ Auth::user()->name }}</p>
            <p><strong>Registrado el:</strong>
                {{ Auth::user()->created_at ? Auth::user()->created_at->format('d/m/Y') : 'N/D' }}</p>

            <div>
                <a href="{{ route('name.form') }}">Modificar nombre</a>
                <a href="{{ route('password.form') }}">Cambiar contraseña</a>
            </div>

            <!-- Mensaje de bienvenida -->
            <p>Desde aquí puedes gestionar tus gastos e ingresos.</p>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit">Cerrar sesión</button>
            </form>
        </div>
    </div>
@endsection