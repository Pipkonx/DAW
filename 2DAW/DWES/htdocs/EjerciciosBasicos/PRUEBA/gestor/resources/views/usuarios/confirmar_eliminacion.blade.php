@extends('plantillas.plantilla')

@section('titulo', 'Confirmar Eliminación')

@section('cuerpo')
    <h1>Confirmar Eliminación de Usuario</h1>

    <aside>
        <p>¿Estás seguro de que deseas eliminar al usuario <strong>{{ $usuario['nombre'] }}</strong> (ID: {{ $usuario['id'] }})?</p>
        <p>Esta acción no se puede deshacer.</p>
    </aside>

    <form action="/EjerciciosBasicos/SERVIDOR/admin/usuarios/eliminar" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $usuario['id'] }}">
        <p>
            <button type="submit">Eliminar</button>
            <a href="/EjerciciosBasicos/SERVIDOR/admin/usuarios" class="button">Cancelar</a>
        </p>
    </form>
@endsection
