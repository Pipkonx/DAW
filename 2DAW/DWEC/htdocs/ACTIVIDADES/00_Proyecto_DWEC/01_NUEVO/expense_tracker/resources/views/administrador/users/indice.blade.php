@extends('layouts.app')

@section('content')
<!-- Lista de usuarios del administrador -->
<div class="user-management-header">
    <h1>Gestión de usuarios</h1>
    <a href="{{ route('usuarios.crear') }}" class="button-primary">Crear nuevo usuario</a>
</div>

@if (session('success'))
    <div class="alert-message success">{{ session('success') }}</div>
@endif

<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($usuarios as $usuario)
            <tr>
                <td>{{ $usuario->id }}</td>
                <td>{{ $usuario->name }}</td>
                <td>{{ $usuario->email }}</td>
                <td>{{ $usuario->role }}</td>
                <td>
                    <a href="{{ route('usuarios.mostrar', $usuario->id) }}" class="button-info button-sm">Ver</a>
                    <a href="{{ route('usuarios.editar', $usuario->id) }}" class="button-warning button-sm">Editar</a>
                    <form action="{{ route('usuarios.destroy', $usuario->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button-danger button-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">Eliminar</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection