@extends('layouts/plantilla01')

@section('titulo', 'Acceder')

@section('cuerpo')
<h1>Acceder</h1>
@if($errors->any())
<div class="error">{{ $errors->first() }}</div>
@endif
<form method="POST" action="{{ url('login') }}">
    @csrf
    <label for="email">Correo</label>
    <input type="text" id="email" name="email" value="{{ old('email') }}">
    <label for="password">Contraseña</label>
    <input type="password" id="password" name="password">
    <label><input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}> Recordar sesión</label>
    <button type="submit" class="btn">Entrar</button>
</form>
@endsection

