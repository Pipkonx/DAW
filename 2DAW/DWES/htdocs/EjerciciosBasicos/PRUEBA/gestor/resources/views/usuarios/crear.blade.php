@extends('plantillas.plantilla')

@section('titulo', 'Crear Usuario')

@section('cuerpo')
    <h1>Crear Nuevo Usuario</h1>

    @if (isset($errores) && count($errores) > 0)
        <aside>
            <ul>
                @foreach ($errores as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </aside>
    @endif

    <form action="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/admin/usuarios/guardar" method="POST">
        @csrf
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required>



        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>

        <label for="rol">Rol:</label>
        <select id="rol" name="rol" required>
            <option value="admin" {{ old('rol') == 'admin' ? 'selected' : '' }}>Administrador</option>
            <option value="operario" {{ old('rol') == 'operario' ? 'selected' : '' }}>Operario</option>
        </select>

        <button type="submit">Crear Usuario</button>
        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/admin/usuarios" class="button">Cancelar</a>
    </form>
@endsection
