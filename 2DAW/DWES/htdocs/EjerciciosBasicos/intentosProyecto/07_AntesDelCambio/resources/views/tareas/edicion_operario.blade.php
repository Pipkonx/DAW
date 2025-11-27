@extends('layouts/plantilla01')

@section('titulo', 'Actualizar tarea (Operario)')

@section('cuerpo')
  <h1>Actualizar tarea (Operario)</h1>
  @if(!empty($errorGeneral))
    <div class="error">{{ $errorGeneral }}</div>
  @endif
  <form action="/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/tareas/editar?id={{ $id }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    <label>Estado:</label><br>
    <select name="estadoTarea">
      <option value="B" {{ (($_POST['estadoTarea'] ?? ($estadoTarea ?? '')) === 'B') ? 'selected' : '' }}>Esperando ser
        aprobada</option>
      <option value="P" {{ (($_POST['estadoTarea'] ?? ($estadoTarea ?? '')) === 'P') ? 'selected' : '' }}>Pendiente</option>
      <option value="R" {{ (($_POST['estadoTarea'] ?? ($estadoTarea ?? '')) === 'R') ? 'selected' : '' }}>Realizada</option>
      <option value="C" {{ (($_POST['estadoTarea'] ?? ($estadoTarea ?? '')) === 'C') ? 'selected' : '' }}>Cancelada</option>
    </select><br>
    @if($msg = \App\Models\Funciones::getError('estadoTarea'))
      <div class="error">{{ $msg }}</div>
    @endif
    <br>

    <label>Anotaciones posteriores:</label><br>
    <textarea
      name="anotacionesPosteriores">{{ htmlspecialchars($_POST['anotacionesPosteriores'] ?? ($anotacionesPosteriores ?? '')) }}</textarea><br>
    @if($msg = \App\Models\Funciones::getError('anotacionesPosteriores'))
      <div class="error">{{ $msg }}</div>
    @endif
    <br><br>

    <label>Fichero resumen:</label>
    <input type="file" name="fichero_resumen"><br><br>

    <label>Fotos (evidencias):</label>
    <input type="file" name="fotos[]" multiple><br><br>

    <button type="submit" class="btn">Guardar</button>
    <a href="/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/tareas" class="btn btn-cancel">Cancelar</a>
  </form>
@endsection