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
    <label>Usuario:</label><br>
    <input type="text" name="usuario" value="{{ $nombre ?? '' }}"><br>

    <label>Contraseña:</label><br>
    <input type="password" name="clave" value="{{ $contraseña ?? '' }}"><br>

    <label class="inline">
      <input type="checkbox" name="guardar_clave" {{ !empty($guardar_clave) ? 'checked' : '' }}> Guardar clave
    </label>

    <div class="nav">
      <button type="submit" class="btn">Entrar</button>
      <a href="/EjerciciosBasicos/intentosProyecto/05_Ejemplo/public/" class="btn btn-cancel">Cancelar</a>
    </div>
  </form>
@endsection
