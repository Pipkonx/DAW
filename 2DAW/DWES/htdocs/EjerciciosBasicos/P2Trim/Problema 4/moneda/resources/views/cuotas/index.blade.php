@extends('layouts.app')

@section('title', 'Cuotas')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
        <h3>Cuotas</h3>
        <div>
            <a href="{{ route('api.prueba') }}" class="btn btn-sm btn-link me-3">Gestionar Clientes</a>
            <span class="me-3 small text-muted">{{ Auth::user()->name }}</span>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary">Salir</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success py-1 small">{{ session('success') }}</div>
    @endif

    <div class="card p-3 mb-4 bg-light">
        <form action="{{ route('cuotas.store') }}" method="POST" class="row g-2">
            @csrf
            <div class="col-md-3">
                <select name="client_id" required class="form-select form-select-sm">
                    <option value="" selected disabled>Cliente...</option>
                    @foreach($clientes as $client)
                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="concept" placeholder="Concepto" required class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <input type="number" step="0.01" name="amount" placeholder="Importe" required class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <select name="currency" required class="form-select form-select-sm">
                    <option value="EUR">EUR</option>
                    <option value="USD">USD</option>
                    <option value="GBP">GBP</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">Generar</button>
            </div>
        </form>
    </div>

    <table class="table table-sm table-hover border">
        <thead class="table-light">
            <tr>
                <th>Cliente</th>
                <th>Concepto</th>
                <th class="text-end">Importe</th>
                <th class="text-center">Estado</th>
                <th class="text-end">EUR</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cuotas as $cuota)
            <tr>
                <td>{{ $cuota->cliente->name }}</td>
                <td class="small text-muted">{{ $cuota->concept }}</td>
                <td class="text-end">{{ number_format($cuota->amount, 2, ',', '.') }} {{ $cuota->currency }}</td>
                <td class="text-center">
                    <span class="badge {{ $cuota->is_paid ? 'bg-success' : 'bg-warning text-dark' }}">
                        {{ $cuota->is_paid ? 'OK' : 'Pte' }}
                    </span>
                </td>
                <td class="text-end fw-bold">
                    {{ $cuota->eur_amount ? number_format($cuota->eur_amount, 2, ',', '.') . ' €' : '-' }}
                </td>
                <td class="text-center">
                    @if(!$cuota->is_paid)
                        <a href="{{ route('pago.paypal', $cuota) }}" class="btn btn-sm btn-outline-primary py-0">Pagar</a>
                    @else
                        <span class="text-muted small">Pagada</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
