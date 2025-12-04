@extends('plantillas.plantilla')

@section('titulo', 'Confirmar Eliminación')

@section('cuerpo')
    <h1>Confirmar Eliminación de Usuario</h1>

    <aside>
        <p>¿Estás seguro de que deseas eliminar al usuario <strong>{{ $usuario['nombre'] }}</strong> (ID: {{ $usuario['id'] }})?</p>
        <p>Esta acción no se puede deshacer.</p>
    </aside>

    <form action="{{ url('/admin/usuarios/eliminar') }}" method="POST">
            @csrf
            <p>¿Estás seguro de que quieres eliminar al usuario {{ $usuario['nombre'] }}?</p>
            <input type="hidden" name="id" value="{{ $usuario['id'] }}">
            <button type="submit" class="button">Eliminar</button>
            <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/admin/usuarios" class="button">Cancelar</a>
    </form>
@endsection
