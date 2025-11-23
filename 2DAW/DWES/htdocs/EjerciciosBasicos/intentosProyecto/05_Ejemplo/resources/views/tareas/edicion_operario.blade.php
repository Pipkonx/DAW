@extends('layouts/plantilla01')

@section('titulo', 'Actualizar tarea (Operario)')

@section('cuerpo')
  <h1>Actualizar tarea (Operario)</h1>
  @if(!empty($errorGeneral))
    <div class="error">{{ $errorGeneral }}</div>
  @endif
  <form action="/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/tareas/{{ $id }}/editar" method="POST"
    enctype="multipart/form-data">
    @csrf
    <label>Estado:</label><br>
    <select name="estadoTarea">
      <option value="B" {{ ($estadoTarea ?? '') === 'B' ? 'selected' : '' }}>Esperando ser aprobada</option>
      <option value="P" {{ ($estadoTarea ?? '') === 'P' ? 'selected' : '' }}>Pendiente</option>
      <option value="R" {{ ($estadoTarea ?? '') === 'R' ? 'selected' : '' }}>Realizada</option>
      <option value="C" {{ ($estadoTarea ?? '') === 'C' ? 'selected' : '' }}>Cancelada</option>
    </select><br><br>

    <label>Anotaciones posteriores:</label><br>
    <textarea name="anotacionesPosteriores">{{ $anotacionesPosteriores ?? '' }}</textarea><br><br>

    <label>Fichero resumen:</label>
    <input type="file" name="fichero_resumen"><br><br>

    <label>Fotos (evidencias):</label>
    <input type="file" name="fotos[]" multiple><br><br>

    <button type="submit" class="btn">Guardar</button>
    <a href="/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/tareas" class="btn btn-cancel">Cancelar</a>
  </form>
@endsection