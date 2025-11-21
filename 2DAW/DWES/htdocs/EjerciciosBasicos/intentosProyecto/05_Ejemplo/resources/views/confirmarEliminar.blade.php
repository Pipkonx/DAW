@extends('layouts/plantilla01')

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
    <a href="/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/tareas" class="btn btn-cancel">Cancelar</a>
    <form action="/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/tareas/{{ $id }}/eliminar" method="POST"
      class="inline">
      @csrf
      <button type="submit" class="btn">Confirmar eliminación</button>
    </form>
  </div>
@endsection