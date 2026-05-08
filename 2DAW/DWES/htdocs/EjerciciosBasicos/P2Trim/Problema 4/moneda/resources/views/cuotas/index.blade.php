@extends('layouts.app')

@section('title', 'Gestión de Cuotas - Nosecaen S.L.')

@section('content')
<div class="panel-gestion">
    <div class="cabecera-panel">
        <h1>Nosecaen S.L. - Cuotas</h1>
        <div>
            <span class="fw-bold me-3 text-secondary">{{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn-salir">Salir</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="tarjeta-formulario">
        <h4>Generar Nueva Cuota</h4>
        <form action="{{ route('cuotas.store') }}" method="POST" class="fila-formulario">
            @csrf
            <div class="columna-formulario">
                <label class="form-label small fw-bold">Cliente</label>
                <select name="client_id" required class="form-select">
                    <option value="" selected disabled>Seleccionar cliente...</option>
                    @foreach($clientes as $client)
                        <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->currency }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Concepto</label>
                <input type="text" name="concept" required class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Importe Local</label>
                <input type="number" step="0.01" name="amount" required class="form-control">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn-generar w-100">Generar</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="tabla-cuotas">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Concepto</th>
                    <th class="text-end">Importe Local</th>
                    <th class="text-center">Estado</th>
                    <th class="text-end">Importe EUR</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cuotas as $cuota)
                <tr>
                    <td class="fw-bold">{{ $cuota->cliente->name }}</td>
                    <td class="text-muted small">{{ $cuota->concept }}</td>
                    <td class="text-end fw-semibold">{{ number_format($cuota->amount, 2) }} {{ $cuota->currency }}</td>
                    <td class="text-center">
                        <span class="{{ $cuota->is_paid ? 'badge-pagado' : 'badge-pendiente' }}">
                            {{ $cuota->is_paid ? 'Pagada' : 'Pendiente' }}
                        </span>
                    </td>
                    <td class="text-end text-primary fw-bold">
                        {{ $cuota->eur_amount ? number_format($cuota->eur_amount, 2) . ' €' : '---' }}
                    </td>
                    <td class="text-center">
                        @if(!$cuota->is_paid)
                            <a href="{{ route('pago.paypal', $cuota) }}" class="btn-pagar">Pagar</a>
                        @else
                            <span class="text-success small">Completado</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
