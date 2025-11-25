@extends('layouts.app')

@section('content')
<div class="form-container">
    <h1>Editar Transacción</h1>

    <form action="{{ route('transacciones.update', $transaccion->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-field">
            <label for="fecha">Fecha:</label>
            <input type="date" class="form-input" id="fecha" name="fecha" value="{{ old('fecha', $transaccion->fecha) }}" required>
            @error('fecha')
                <div class="alert-message error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-field">
            <label for="tipo">Tipo:</label>
            <select class="form-input" id="tipo" name="tipo" required>
                <option value="ingreso" {{ old('tipo', $transaccion->tipo) == 'ingreso' ? 'selected' : '' }}>Ingreso</option>
                <option value="gasto" {{ old('tipo', $transaccion->tipo) == 'gasto' ? 'selected' : '' }}>Gasto</option>
            </select>
            @error('tipo')
                <div class="alert-message error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-field">
            <label for="categori-id">Categoría:</label>
            <select class="form-input" id="categori-id" name="categori-id" required>
                @foreach ($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ old('categori-id', $transaccion->category_id) == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                @endforeach
            </select>
            @error('categori-id')
                <div class="alert-message error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-field">
            <label for="monto">Monto:</label>
            <input type="number" class="form-input" id="monto" name="monto" value="{{ old('monto', $transaccion->monto) }}" step="0.01" required>
            @error('monto')
                <div class="alert-message error">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-field">
            <label for="descripcion">Descripción:</label>
            <textarea class="form-input" id="descripcion" name="descripcion">{{ old('descripcion', $transaccion->descripcion) }}</textarea>
            @error('descripcion')
                <div class="alert-message error">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="button-primary">Actualizar Transacción</button>
        <a href="{{ route('transacciones.index') }}" class="button-secondary">Cancelar</a>
    </form>
</div>
@endsection