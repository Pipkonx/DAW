@extends('plantillas.plantilla')

@section('titulo', 'Gestión de Usuarios')

@section('cuerpo')
    <h1>Gestión de Usuarios</h1>

    @if (session('success'))
        <aside>
            {{ session('success') }}
        </aside>
    @endif

    @if (session('error'))
        <aside>
            {{ session('error') }}
        </aside>
    @endif

    <p>
        <a href="/EjerciciosBasicos/SERVIDOR/admin/usuarios/crear" class="button">Crear Nuevo Usuario</a>
        <a href="/EjerciciosBasicos/SERVIDOR/admin/tareas" class="button">Volver a Tareas</a>
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
                        <a href="/EjerciciosBasicos/SERVIDOR/admin/usuarios/editar?id={{ $usuario['id'] }}" class="button">Editar</a>
                        <a href="/EjerciciosBasicos/SERVIDOR/admin/usuarios/confirmarEliminar?id={{ $usuario['id'] }}" class="button">Eliminar</a>
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
            <a href="/EjerciciosBasicos/SERVIDOR/admin/usuarios?pagina={{ $i }}" class="button {{ $i == $paginaActual ? 'active' : '' }}">{{ $i }}</a>
        @endfor
    </div>
@endsection
