@extends('layouts/plantilla01')

@section('titulo', 'Usuario')

@section('cuerpo')
  <h1>{{ $titulo ?? (isset($id) ? 'Editar usuario' : 'Crear usuario') }}</h1>
  @if(!empty($errorGeneral))
    <div class="error">{{ $errorGeneral }}</div>
  @endif
  <form action="{{ $formActionUrl ?? (isset($id) ? '/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/usuarios/editar?id='.$id : '/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/usuarios/crear') }}" method="POST">
    @csrf
    <label>Usuario:</label><br>
    <input type="text" name="usuario" value="{{ $usuario ?? '' }}"><br>
    <label>Clave (plana):</label><br>
    <input type="text" name="clave" value="{{ $clave ?? '' }}"><br>
    @if (!isset($hideRole) || !$hideRole)
    <label>Rol:</label><br>
    <div class="radio-group">
      <span class="radio-group-item">
        <input type="radio" id="rol_administrador" name="rol" value="administrador" {{ (isset($rol) && $rol == 'administrador') ? 'checked' : '' }}>
        <label for="rol_administrador">Administrador</label>
      </span>
      <span class="radio-group-item">
        <input type="radio" id="rol_operario" name="rol" value="operario" {{ (isset($rol) && $rol == 'operario') ? 'checked' : '' }}>
        <label for="rol_operario">Operario</label>
      </span>
    </div>
    <br>
    @endif
    <button type="submit" class="btn">Guardar</button>
    <a href="{{ $cancelUrl ?? '/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/usuarios' }}" class="btn btn-cancel">Cancelar</a>
  </form>
@endsection
