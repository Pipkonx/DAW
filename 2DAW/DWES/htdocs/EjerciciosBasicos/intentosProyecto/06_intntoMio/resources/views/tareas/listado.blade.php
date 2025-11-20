@extends('layouts.main')

@section('title', 'Listado de Tareas')

@section('content')
<h2>Listado de Tareas</h2>
<a href="/tasks/create">Añadir Nueva Tarea</a>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>NIF</th>
        <th>Persona Contacto</th>
        <th>Teléfono</th>
        <th>Operario</th>
        <th>Fecha Realización</th>
        <th>Acciones</th>
    </tr>
    @foreach($tareas as $tarea)
    <tr>
        <td>{{ $tarea['id'] }}</td>
        <td>{{ $tarea['nif'] }}</td>
        <td>{{ $tarea['persona_contacto'] }}</td>
        <td>{{ $tarea['telefono'] }}</td>
        <td>{{ $tarea['operario'] }}</td>
        <td>{{ $tarea['fecha_realizacion'] }}</td>
        <td>
            <a href="/tasks/view?id={{ $tarea['id'] }}">Ver</a> |
            <a href="/tasks/edit?id={{ $tarea['id'] }}">Editar</a> |
            <a href="/tasks/delete?id={{ $tarea['id'] }}" onclick="return confirm('¿Seguro que quieres eliminar esta tarea?');">Eliminar</a>
        </td>
    </tr>
    @endforeach
</table>
@endsection
