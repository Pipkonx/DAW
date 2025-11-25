@extends('layouts.app')

@section('content')
<!-- Formulario de creación de usuario (administración) -->
<h1>Crear nuevo usuario</h1>

@if ($errors->any())
    <div class="alert-message error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('usuarios.store') }}" method="POST">
    @csrf
    <div class="form-field">
        <label for="name" class="form-label">Nombre</label>
        <input type="text" class="form-input" id="name" name="name" value="{{ old('name') }}" required>
    </div>
    <div class="form-field">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" class="form-input" id="email" name="email" value="{{ old('email') }}" required>
    </div>
    <div class="form-field">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-input" id="password" name="password" required>
    </div>
    <div class="form-field">
        <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
        <input type="password" class="form-input" id="password_confirmation" name="password_confirmation" required>
    </div>
    <div class="form-field">
        <label for="role" class="form-label">Rol</label>
        <select class="form-input" id="role" name="role" required>
            <option value="operator" {{ old('role') == 'operator' ? 'selected' : '' }}>Operador</option>
            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
        </select>
    </div>
    <button type="submit" class="button-primary">Crear usuario</button>
    <a href="{{ route('usuarios.indice') }}" class="button-secondary">Cancelar</a>
</form>
@endsection