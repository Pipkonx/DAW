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

    <form action="/EjerciciosBasicos/SERVIDOR/admin/usuarios/crear" method="POST">
        @csrf
        <p>
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $usuario->nombre ?? '') }}" required>
        </p>
        <p>
            <label for="contraseña">Contraseña</label>
            <input type="password" id="contraseña" name="contraseña" required>
        </p>
        <p>
            <label for="confirmar_contraseña">Confirmar Contraseña</label>
            <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" required>
        </p>
        <p>
            <label for="rol">Rol</label>
            <select id="rol" name="rol" required>
                <option value="admin" {{ (old('rol', $usuario->rol ?? '') == 'admin') ? 'selected' : '' }}>Administrador</option>
                <option value="operario" {{ (old('rol', $usuario->rol ?? '') == 'operario') ? 'selected' : '' }}>Operario</option>
            </select>
        </p>
        <p>
            <button type="submit">Guardar</button>
            <a href="/EjerciciosBasicos/SERVIDOR/admin/usuarios" class="button">Cancelar</a>
        </p>
    </form>
@endsection
