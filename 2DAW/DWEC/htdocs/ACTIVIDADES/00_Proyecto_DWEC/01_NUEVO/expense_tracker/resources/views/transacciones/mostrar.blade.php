@extends('layouts.app')

@section('content')
<div class="form-container">
    <h1>Detalles de la Transacción</h1>

    <div class="card-container">
        <div class="card-header">
            Transacción #{{ $transaction->id }}
        </div>
        <div class="card-body">
            <p><strong>Fecha:</strong> {{ $transaction->fecha }}</p>
            <p><strong>Tipo:</strong> {{ $transaction->tipo }}</p>
            <p><strong>Categoría:</strong> {{ $transaction->categoria->nombre }}</p>
            <p><strong>Monto:</strong> {{ $transaction->monto }}</p>
            <p><strong>Descripción:</strong> {{ $transaction->descripcion }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('transacciones.editar', $transaction->id) }}" class="button-warning">Editar</a>
            <form action="{{ route('transacciones.destroy', $transaction->id) }}" method="POST" class="inline-block-display">
                @csrf
                @method('DELETE')
                <button type="submit" class="button-danger" onclick="return confirm('¿Estás seguro de eliminar esta transacción?')">Eliminar</button>
            </form>
            <a href="{{ route('transacciones.index') }}" class="button-secondary">Volver a la lista</a>
        </div>
    </div>
</div>
@endsection