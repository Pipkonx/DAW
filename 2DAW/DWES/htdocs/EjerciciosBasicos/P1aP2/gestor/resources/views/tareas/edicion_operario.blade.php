@extends('plantillas.plantilla')

@section('titulo', 'Actualizar tarea (Operario)')

@section('cuerpo')
  <h1>Actualizar tarea (Operario)</h1>
  @if(!empty($errorGeneral))
    <div class="error">{{ $errorGeneral }}</div>
  @endif
  
  <div class="form-container">
  <form action="{{ $formActionUrl }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($id))<input type="hidden" name="id" value="{{ $id }}">@endif

    <!-- Read Only Details -->
    <div class="details-section">
        <p><strong>NIF/CIF:</strong> {{ $nifCif ?? '' }}</p>
        <p><strong>Persona:</strong> {{ $personaNombre ?? '' }}</p>
        <p><strong>Teléfono:</strong> {{ $telefono ?? '' }}</p>
        <p><strong>Correo:</strong> {{ $correo ?? '' }}</p>
        <p><strong>Descripción:</strong> {{ $descripcionTarea ?? '' }}</p>
        <p><strong>Dirección:</strong> {{ $direccionTarea ?? '' }}, {{ $poblacion ?? '' }} ({{ $provincia ?? '' }})</p>
    </div>
    <hr>

    <!-- Editable Fields -->
    <label>Estado:</label><br>
    <select name="estadoTarea">
      <option value="R" {{ (($estadoTarea ?? '') == 'R') ? 'selected' : '' }}>Completada</option>
      <option value="C" {{ (($estadoTarea ?? '') == 'C') ? 'selected' : '' }}>Cancelada</option>
    </select><br>
    @if($msg = \App\Models\M_Funciones::getError('estadoTarea'))
      <div class="error">{{ $msg }}</div>
    @endif
    <br>

    <label>Fecha de realización:</label><br>
    <input type="date" name="fechaRealizacion" value="{{ $fechaRealizacion ?? '' }}"><br>
    @if($msg = \App\Models\M_Funciones::getError('fecha_realizacion'))
        <div class="error">{{ $msg }}</div>
    @endif
    <br>

    <label>Anotaciones anteriores:</label><br>
    <textarea readonly disabled>{{ $anotacionesAnteriores ?? '' }}</textarea>
    <input type="hidden" name="anotacionesAnteriores" value="{{ $anotacionesAnteriores ?? '' }}">
    <br><br>

    <label>Anotaciones posteriores:</label><br>
    <textarea name="anotacionesPosteriores">{{ $anotacionesPosteriores ?? '' }}</textarea><br>
    @if($msg = \App\Models\M_Funciones::getError('anotacionesPosteriores'))
      <div class="error">{{ $msg }}</div>
    @endif
    <br><br>

    <!-- Files -->
    <label>Fichero resumen:</label><br>
    @if(!empty($ficherosResumen))
        <div class="file-item">
            <a href="{{ url('evidencias/' . $id . '/' . $ficherosResumen[0]) }}" target="_blank">{{ $ficherosResumen[0] }}</a>
            <button type="submit" form="form-delete-resumen" class="btn-danger" style="font-size: 0.8em; padding: 2px 5px;">Eliminar</button>
        </div>
    @else
        <input type="file" name="fichero_resumen"><br>
    @endif
    <br>

    <label>Fotos (evidencias):</label><br>
    @if(!empty($fotos))
        @foreach($fotos as $foto)
            <div class="file-item">
                <a href="{{ url('evidencias/' . $id . '/' . $foto) }}" target="_blank">{{ $foto }}</a>
                <button type="submit" form="form-delete-foto-{{ md5($foto) }}" class="btn-danger" style="font-size: 0.8em; padding: 2px 5px;">Eliminar</button>
            </div>
        @endforeach
    @endif
    <input type="file" name="fotos[]" multiple><br><br>

    <button type="submit" class="btn">Guardar</button>
    <a href="{{ url('operario/tareas') }}" class="btn btn-cancel">Cancelar</a>
  </form>

  <!-- Hidden Delete Forms -->
  @if(!empty($ficherosResumen))
     <form id="form-delete-resumen" action="{{ url('operario/tareas/eliminar-fichero') }}" method="POST" style="display:none;">
         @csrf
         <input type="hidden" name="id" value="{{ $id }}">
         <input type="hidden" name="filename" value="{{ $ficherosResumen[0] }}">
     </form>
  @endif
  @if(!empty($fotos))
    @foreach($fotos as $foto)
        <form id="form-delete-foto-{{ md5($foto) }}" action="{{ url('operario/tareas/eliminar-fichero') }}" method="POST" style="display:none;">
            @csrf
            <input type="hidden" name="id" value="{{ $id }}">
            <input type="hidden" name="filename" value="{{ $foto }}">
        </form>
    @endforeach
  @endif
  </div>
@endsection
