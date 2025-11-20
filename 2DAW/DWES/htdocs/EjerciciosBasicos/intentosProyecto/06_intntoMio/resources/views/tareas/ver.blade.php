@extends('layouts.main')

@section('title', 'Ver Tarea')

@section('content')
<h2>Detalle de Tarea</h2>
<ul>
    <li>ID: {{ $tarea['id'] }}</li>
    <li>NIF: {{ $tarea['nif'] }}</li>
    <li>Persona contacto: {{ $tarea['persona_contacto'] }}</li>
    <li>Teléfono: {{ $tarea['telefono'] }}</li>
    <li>Descripción: {{ $tarea['descripcion'] }}</li>
    <li>Email: {{ $tarea['email'] }}</li>
    <li>Dirección: {{ $tarea['direccion'] }}</li>
    <li>Población: {{ $tarea['poblacion'] }}</li>
    <li>CP: {{ $tarea['cp'] }}</li>
    <li>Provincia: {{ $tarea['provincia'] }}</li>
    <li>Estado: {{ $tarea['estado'] }}</li>
    <li>Operario: {{ $tarea['operario'] }}</li>
    <li>Fecha realización: {{ $tarea['fecha_realizacion'] }}</li>
    @if(!empty($tarea['archivo']))
        <li>Archivo: <a href="/storage/uploads/{{ $tarea['archivo'] }}" target="_blank">{{ $tarea['archivo'] }}</a></li>
    @endif
</ul>
<a href="/tasks">Volver a la lista</a>
@endsection
