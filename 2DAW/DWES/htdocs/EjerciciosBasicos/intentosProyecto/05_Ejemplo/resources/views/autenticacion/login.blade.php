@extends('layouts/plantilla01')

@section('titulo', 'Login')

@section('cuerpo')
  <h1>Login</h1>
  @if(!empty($errorGeneral))
    <div class="error">{{ $errorGeneral }}</div>
  @endif
  @if(!empty($mensaje))
    <div class="msg">{{ $mensaje }}</div>
  @endif

  <form action="login" method="POST">
    @csrf
        <div class="form-group-inline">
      <label class="form-label-inline">Usuario:</label>
      <input type="text" name="usuario" value="{{ $nombre ?? '' }}">
    </div>

        <div class="form-group-inline">
      <label class="form-label-inline">Contraseña:</label>
      <input type="password" name="clave" value="{{ $contraseña ?? '' }}">
    </div>
        <div class="form-group-inline">
      <label class="inline">
        <input type="checkbox" name="guardar_clave" {{ !empty($guardar_clave) ? 'checked' : '' }}> Guardar clave
      </label>
    </div>

    <div class="nav">
      <button type="submit" class="btn">Entrar</button>

    </div>
  </form>
@endsection