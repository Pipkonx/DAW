<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tareas</title>
    <link rel="stylesheet" href="/css/style.css">
    </head>
<body>
    <div class="container">
        <h1>Gestión de Tareas</h1>

        <div class="menu">
            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Ver Usuarios</a>
        </div>

        <div class="filters">
            <form method="GET" action="{{ route('tareas.index') }}">
                <label for="usuario_id">Usuario</label>
                <select id="usuario_id" name="usuario_id">
                    <option value="">Todos los usuarios</option>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario['id'] }}" @selected($usuario['id'] == $filtroUsuario)>
                            {{ $usuario['nombre'] }}
                        </option>
                    @endforeach
                </select>

                <label for="completada">Estado</label>
                <select id="completada" name="completada">
                    <option value="">Todas</option>
                    <option value="1" @selected($filtroCompletada === '1')>Completadas</option>
                    <option value="0" @selected($filtroCompletada === '0')>Pendientes</option>
                </select>

                <button class="btn btn-primary" type="submit">Filtrar</button>
            </form>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Descripción</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Completada</th>
                    <th>Fecha Creación</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tareas as $tarea)
                    <tr>
                        <td>{{ $tarea['id'] }}</td>
                        <td>{{ $tarea['titulo'] }}</td>
                        <td>{{ $tarea['descripcion'] }}</td>
                        <td>{{ $tarea['nombre_usuario'] ?? '' }}</td>
                        <td>{{ $tarea['email_usuario'] ?? '' }}</td>
                        <td>{{ isset($tarea['completada']) && $tarea['completada'] ? 'Sí' : 'No' }}</td>
                        <td>{{ $tarea['fecha_creacion'] ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No hay tareas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </body>
</html>