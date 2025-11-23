@extends('layouts/plantilla01')

@section('titulo', 'Detalle de tarea')

@section('cuerpo')
  <h1>Detalle de tarea #{{ $id }}</h1>
  <table>
    <tr><th>Persona</th><td>{{ $personaNombre }}</td></tr>
    <tr><th>Correo</th><td>{{ $correo }}</td></tr>
    <tr><th>Teléfono</th><td>{{ $telefono }}</td></tr>
    <tr><th>Descripción</th><td>{{ $descripcionTarea }}</td></tr>
    <tr><th>Dirección</th><td>{{ $direccionTarea }}</td></tr>
    <tr><th>Población</th><td>{{ $poblacion }}</td></tr>
    <tr><th>Código Postal</th><td>{{ $codigoPostal }}</td></tr>
    <tr><th>Provincia</th><td>{{ $provincia }}</td></tr>
    <tr><th>Estado</th><td>{{ $estadoTarea }}</td></tr>
    <tr><th>Operario</th><td>{{ $operarioEncargado }}</td></tr>
    <tr><th>Fecha realización</th><td>{{ $fechaRealizacion }}</td></tr>
    <tr><th>Anotaciones anteriores</th><td>{{ $anotacionesAnteriores }}</td></tr>
    <tr><th>Anotaciones posteriores</th><td>{{ $anotacionesPosteriores }}</td></tr>
  </table>
  <div class="nav">
    <a href="/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/tareas" class="btn">Volver</a>
    <a href="/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/tareas/{{ $id }}/editar" class="btn">Editar</a>
  </div>
@endsection
