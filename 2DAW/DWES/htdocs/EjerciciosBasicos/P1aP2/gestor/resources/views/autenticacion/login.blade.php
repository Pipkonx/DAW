@extends('plantillas.plantilla')

@section('titulo', 'Login')

@section('cuerpo')
  <h1>Login</h1>
  @if(!empty($errorGeneral))
    <aside>
      {{ $errorGeneral }}
    </aside>
  @endif
  @if(!empty($mensaje))
    <aside>
      {{ $mensaje }}
    </aside>
  @endif

  <form action="{{ url('/login') }}" method="POST">
    @csrf
    <p>
      <label>Usuario:</label>
      <input type="text" name="usuario" value="{{ $nombre ?? '' }}">
    </p>

    <p>
      <label>Contraseña:</label>
      <input type="password" name="clave" value="{{ $contraseña ?? '' }}">
    </p>
    
    <p>
      <label>
        <input type="checkbox" name="guardar_clave" {{ !empty($guardar_clave) ? 'checked' : '' }}> Guardar clave
      </label>
    </p>

    <p>
      <button type="submit">Entrar</button>
    </p>
  </form>
@endsection
