<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Generar Remesa Mensual') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="text-center mb-5">
                    <i class="fas fa-file-invoice-dollar text-success mb-4" style="font-size: 4rem;"></i>
                    <h3>Generación de Remesa de Cuotas</h3>
                    <p class="text-muted">Se generarán las cuotas mensuales para todos los clientes activos.</p>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h5 class="card-title mb-0">Resumen de la Remesa</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush mb-4">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Periodo:
                                        <span class="fw-bold">{{ $month }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Clientes Activos:
                                        <span class="badge bg-primary rounded-pill">{{ $client_count }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Concepto:
                                        <span class="text-muted">Cuota mensual mantenimiento - {{ $month }}</span>
                                    </li>
                                </ul>

                                <form action="{{ route('fees.remittance.store') }}" method="POST">
                                    @csrf
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('¿Confirmar generación de remesa?')">
                                            Confirmar y Generar Remesa
                                        </button>
                                        <a href="{{ route('fees.index') }}" class="btn btn-outline-secondary">
                                            Cancelar
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>