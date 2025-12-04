@extends('plantillas.plantilla')

@section('titulo', 'Confirmar eliminación')

@section('cuerpo')
  <h1>Confirmar eliminación</h1>
  <p>Vas a eliminar la tarea <strong>#{{ $id }}</strong>.</p>
  <ul>
    <li><strong>Persona:</strong> {{ $personaNombre ?? '' }}</li>
    <li><strong>Descripción:</strong> {{ $descripcionTarea ?? '' }}</li>
    <li><strong>Fecha:</strong> {{ $fechaRealizacion ?? '' }}</li>
    <li><strong>Estado:</strong> {{ $estadoTarea ?? '' }}</li>
  </ul>

  <div class="nav">
    <a href="{{ url('admin/tareas') }}" class="btn btn-cancel">Cancelar</a>
    <form action="{{ url('admin/tareas/eliminar') }}" method="POST"
      class="inline">
      @csrf
      <input type="hidden" name="id" value="{{ $id }}">
      <button type="submit" class="btn">Confirmar eliminación</button>
    </form>
  </div>
@endsection