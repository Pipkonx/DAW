@extends('layouts.app')

@section('content')
<!-- Detalle del usuario (administración) -->
<h1>Detalles del usuario</h1>

<div class="card">
    <div class="card-header">Información del usuario</div>
    <div class="card-body">
        <p><strong>ID:</strong> {{ $user->id }}</p>
        <p><strong>Nombre:</strong> {{ $user->name }}</p>
        <p><strong>Email:</strong> {{ $user->email }}</p>
        <p><strong>Rol:</strong> {{ $user->role }}</p>
        <p><strong>Fecha de creación:</strong> {{ $user->created_at }}</p>
        <p><strong>Última actualización:</strong> {{ $user->updated_at }}</p>
    </div>
</div>

<a href="{{ route('usuarios.indice') }}" class="btn btn-primary mt-3">Volver a la lista</a>
@endsection
