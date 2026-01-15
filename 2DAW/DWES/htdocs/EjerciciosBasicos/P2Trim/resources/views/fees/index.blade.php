<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gestión de Cuotas y Facturación') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="d-flex justify-content-between mb-4">
                    <h3>Registro de Cuotas</h3>
                    <div>
                        <a href="{{ route('fees.remittance') }}" class="btn btn-success me-2">
                            <i class="fas fa-file-invoice-dollar"></i> Generar Remesa
                        </a>
                        <a href="{{ route('fees.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Cuota Excepcional
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Concepto</th>
                                <th>Fecha</th>
                                <th>Importe</th>
                                <th>Pagado</th>
                                <th>Factura</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fees as $fee)
                                <tr>
                                    <td>{{ $fee->id }}</td>
                                    <td>{{ $fee->client->name }}</td>
                                    <td>{{ $fee->concept }}</td>
                                    <td>{{ $fee->emission_date }}</td>
                                    <td>{{ number_format($fee->amount, 2) }} {{ $fee->client->currency }}</td>
                                    <td>
                                        <span class="badge {{ $fee->is_paid == 'S' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $fee->is_paid == 'S' ? 'SÍ' : 'NO' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('fees.invoice', $fee) }}" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                    </td>
                                    <td>
                                        <form action="{{ route('fees.destroy', $fee) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>