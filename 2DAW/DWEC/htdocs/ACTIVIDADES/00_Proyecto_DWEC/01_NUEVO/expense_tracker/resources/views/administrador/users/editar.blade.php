@extends('layouts.app')

@section('content')
<!-- Formulario de edición de usuario (administración) -->
<h1>Editar usuario</h1>

@if ($errors->any())
    <div class="alert-message error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('usuarios.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="form-field">
        <label for="name" class="form-label">Nombre</label>
        <input type="text" class="form-input" id="name" name="name" value="{{ old('name', $user->name) }}" required>
    </div>
    <div class="form-field">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" class="form-input" id="email" name="email" value="{{ old('email', $user->email) }}" required>
    </div>
    <div class="form-field">
        <label for="role" class="form-label">Rol</label>
        <select class="form-input" id="role" name="role" required>
            <option value="operator" {{ old('role', $user->role) == 'operator' ? 'selected' : '' }}>Operador</option>
            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
        </select>
    </div>
    <button type="submit" class="button-primary">Actualizar usuario</button>
    <a href="{{ route('usuarios.indice') }}" class="button-secondary">Cancelar</a>
</form>
@endsection