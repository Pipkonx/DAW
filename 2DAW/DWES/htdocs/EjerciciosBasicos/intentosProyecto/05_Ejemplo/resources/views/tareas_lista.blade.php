@extends('layouts/plantilla01')

@section('titulo','Listado de Tareas')

@section('cuerpo')
<div class="nav">
  <a href="{!! url('tareas/crear') !!}" class="btn">Crear nueva tarea</a>
</div>

<h1>Tareas</h1>
@if(!empty($errorGeneral))
<div class="error">{{ $errorGeneral }}</div>
@endif
@if(!empty($mensaje))
<div class="msg">{{ $mensaje }}</div>
@endif

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Persona</th>
      <th>Descripción</th>
      <th>Fecha</th>
      <th>Estado</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
  @forelse($tareas as $t)
    <tr>
      <td>{{ $t['id'] }}</td>
      <td>{{ $t['personaNombre'] }}</td>
      <td>{{ $t['descripcionTarea'] }}</td>
      <td>{{ $t['fechaRealizacion'] }}</td>
      <td>{{ $t['estadoTarea'] }}</td>
      <td>
        <a href="{!! url('tareas/'.$t['id'].'/editar') !!}">Editar</a>
        <form action="{!! url('tareas/'.$t['id'].'/eliminar') !!}" method="POST" class="inline">
          @csrf
          <button type="submit" class="btn">Eliminar</button>
        </form>
      </td>
    </tr>
  @empty
    <tr><td colspan="6">No hay tareas</td></tr>
  @endforelse
  </tbody>
  </table>
@endsection