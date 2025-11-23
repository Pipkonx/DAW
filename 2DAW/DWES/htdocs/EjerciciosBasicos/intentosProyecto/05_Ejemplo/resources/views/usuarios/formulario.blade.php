@extends('layouts/plantilla01')

@section('titulo', 'Usuario')

@section('cuerpo')
  <h1>{{ isset($id) ? 'Editar usuario' : 'Crear usuario' }}</h1>
  @if(!empty($errorGeneral))
    <div class="error">{{ $errorGeneral }}</div>
  @endif
  <form action="{{ isset($id) ? 'usuarios/'.$id.'/editar' : 'usuarios/crear' }}" method="POST">
    @csrf
    <label>Usuario:</label><br>
    <input type="text" name="usuario" value="{{ $usuario ?? '' }}"><br>
    <label>Clave (plana):</label><br>
    <input type="text" name="clave" value="{{ $clave ?? '' }}"><br>
    <label>Rol:</label><br>
    <select name="rol">
      <option value="operario" {{ ($rol ?? '') === 'operario' ? 'selected' : '' }}>Operario</option>
      <option value="admin" {{ ($rol ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
    </select><br><br>
    <button type="submit" class="btn">Guardar</button>
    <a href="usuarios" class="btn btn-cancel">Cancelar</a>
  </form>
@endsection
