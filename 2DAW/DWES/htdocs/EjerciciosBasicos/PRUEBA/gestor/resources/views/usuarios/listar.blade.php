<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$mensaje = $_SESSION['mensaje'] ?? '';
unset($_SESSION['mensaje']);
$errorGeneral = $_SESSION['errorGeneral'] ?? '';
unset($_SESSION['errorGeneral']);
?>
@extends('plantillas.plantilla')

@section('titulo', 'Gestión de Usuarios')

@section('cuerpo')
    <h1>Gestión de Usuarios</h1>

    @if ($mensaje)
        <aside>
            {{ $mensaje }}
        </aside>
    @endif

    @if ($errorGeneral)
        <aside>
            {{ $errorGeneral }}
        </aside>
    @endif

    <p>
        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/admin/usuarios/crear" class="button">Crear Nuevo Usuario</a>
        <a href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/admin/tareas" class="button">Volver a Tareas</a>
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario['id'] }}</td>
                    <td>{{ $usuario['nombre'] }}</td>
                    <td>{{ $usuario['rol'] }}</td>
                    <td>
                        <a href="{{ url('/admin/usuarios/editar/' . $usuario['id']) }}" class="button">Editar</a>
                        <a href="{{ url('/admin/usuarios/confirmarEliminar/' . $usuario['id']) }}" class="button">Eliminar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No hay usuarios registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pagination">
        @for ($i = 1; $i <= $totalPaginas; $i++)
            <a href="{{ url('/admin/usuarios', ['pagina' => $i]) }}" class="button {{ $i == $paginaActual ? 'active' : '' }}">{{ $i }}</a>
        @endfor
    </div>
@endsection
