@extends('layouts.main')

@section('title', 'Editar Tarea')

@section('content')
<h2>Editar Tarea</h2>
<form method="POST" action="/tasks/edit">
    @csrf
    <input type="hidden" name="id" value="{{ $tarea['id'] }}">
    <label>NIF: <input type="text" name="nif" value="{{ $tarea['nif'] }}" required></label><br>
    <label>Persona contacto: <input type="text" name="persona_contacto" value="{{ $tarea['persona_contacto'] }}" required></label><br>
    <label>Teléfono: <input type="text" name="telefono" value="{{ $tarea['telefono'] }}" required></label><br>
    <label>Descripción: <textarea name="descripcion">{{ $tarea['descripcion'] }}</textarea></label><br>
    <label>Email: <input type="email" name="email" value="{{ $tarea['email'] }}"></label><br>
    <label>Dirección: <input type="text" name="direccion" value="{{ $tarea['direccion'] }}"></label><br>
    <label>Población: <input type="text" name="poblacion" value="{{ $tarea['poblacion'] }}"></label><br>
    <label>CP: <input type="text" name="cp" value="{{ $tarea['cp'] }}"></label><br>
    <label>Provincia: <input type="text" name="provincia" value="{{ $tarea['provincia'] }}"></label><br>
    <label>Estado: <input type="text" name="estado" value="{{ $tarea['estado'] }}"></label><br>
    <label>Operario: <input type="text" name="operario" value="{{ $tarea['operario'] }}" required></label><br>
    <label>Fecha realización: <input type="date" name="fecha_realizacion" value="{{ $tarea['fecha_realizacion'] }}"></label><br>
    <button type="submit">Guardar Cambios</button>
</form>
@endsection
