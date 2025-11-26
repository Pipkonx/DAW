@extends('layouts/plantilla01')

@section('titulo', 'Gestión de usuarios')

@section('cuerpo')
  <div class="nav">
    <a href="/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/usuarios/crear" class="btn">Crear usuario</a>
  </div>
  <h1>Usuarios</h1>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Rol</th>
        <th>Guardar clave</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
    @forelse($usuarios as $u)
      <tr>
        <td>{{ $u['id'] }}</td>
        <td>{{ $u['usuario'] }}</td>
        <td>{{ $u['rol'] }}</td>
        <td>{{ !empty($u['guardar_clave']) ? 'Sí' : 'No' }}</td>
        <td>
          <a href="/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/usuarios/editar?id={{ $u['id'] }}" class="btn inline">Editar</a>
          <form action="/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/usuarios/eliminar" method="POST" class="inline">
            @csrf
            <input type="hidden" name="id" value="{{ $u['id'] }}">
            <button type="submit" class="btn">Eliminar</button>
          </form>
        </td>
      </tr>
    @empty
      <tr><td colspan="5">No hay usuarios</td></tr>
    @endforelse
    </tbody>
  </table>
@endsection
