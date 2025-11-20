@extends('layouts.main')

@section('title', 'Añadir Tarea')

@section('content')
<h2>Añadir Nueva Tarea</h2>
<form method="POST" action="/tasks/create" enctype="multipart/form-data">
    @csrf
    <label>NIF: <input type="text" name="nif" required></label><br>
    <label>Persona contacto: <input type="text" name="persona_contacto" required></label><br>
    <label>Teléfono: <input type="text" name="telefono" required></label><br>
    <label>Descripción: <textarea name="descripcion" required></textarea></label><br>
    <label>Email: <input type="email" name="email"></label><br>
    <label>Dirección: <input type="text" name="direccion"></label><br>
    <label>Población: <input type="text" name="poblacion"></label><br>
    <label>CP: <input type="text" name="cp"></label><br>
    <label>Provincia: <input type="text" name="provincia"></label><br>
    <label>Operario: <input type="text" name="operario" required></label><br>
    <label>Fecha realización: <input type="date" name="fecha_realizacion"></label><br>
    <label>Adjuntar archivo: <input type="file" name="archivo"></label><br>
    <button type="submit">Guardar Tarea</button>
</form>
@endsection
