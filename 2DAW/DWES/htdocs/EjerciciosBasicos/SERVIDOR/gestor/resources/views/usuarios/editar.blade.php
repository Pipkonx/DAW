@extends('plantillas.plantilla')

@section('titulo', 'Editar Usuario')

@section('cuerpo')
    <h1>Editar Usuario</h1>

    @if (isset($errores) && count($errores) > 0)
        <aside>
            <ul>
                @foreach ($errores as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </aside>
    @endif

    <form action="/EjerciciosBasicos/SERVIDOR/admin/usuarios/editar" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{ $usuario['id'] }}">
        <p>
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $usuario['nombre'] ?? '') }}" required>
        </p>
        <p>
            <label for="contraseña">Nueva Contraseña (obligatoria)</label>
            <input type="password" id="contraseña" name="contraseña" required>
        </p>
        <p>
            <label for="confirmar_contraseña">Confirmar Nueva Contraseña</label>
            <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" required>
        </p>
        <p>
            <label for="rol">Rol</label>
            <select id="rol" name="rol" required>
                <option value="admin" {{ (old('rol', $usuario['rol'] ?? '') == 'admin') ? 'selected' : '' }}>Administrador</option>
                <option value="operario" {{ (old('rol', $usuario['rol'] ?? '') == 'operario') ? 'selected' : '' }}>Operario</option>
            </select>
        </p>
        <p>
            <button type="submit">Actualizar</button>
            <a href="/EjerciciosBasicos/SERVIDOR/admin/usuarios" class="button">Cancelar</a>
        </p>
    </form>
@endsection
