@extends('layouts.app')

@section('content')
<div class="table-container">
    <h1>Lista de Transacciones</h1>
    <a href="{{ route('transacciones.crear') }}" class="button-primary">Crear Nueva Transacción</a>

    @if (session('success'))
        <div class="alert-message success">
            {{ session('success') }}
        </div>
    @endif

    <table class="data-table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Categoría</th>
                <th>Monto</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transacciones as $transaccion)
            <tr>
                <td>{{ $transaccion->fecha }}</td>
                <td>{{ $transaccion->tipo }}</td>
                <td>{{ $transaccion->categoria->nombre }}</td>
                <td>{{ $transaccion->monto }}</td>
                <td>{{ $transaccion->descripcion }}</td>
                <td>
                    <a href="{{ route('transacciones.editar', $transaccion->id) }}" class="button-warning button-sm">Editar</a>
                    <form action="{{ route('transacciones.destroy', $transaccion->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="button-danger button-sm" onclick="return confirm('¿Estás seguro de eliminar esta transacción?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">No hay transacciones registradas.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection