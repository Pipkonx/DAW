<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @php
                $missingFields = [];
                if (empty(auth()->user()->dni)) $missingFields[] = 'DNI';
                if (empty(auth()->user()->cif)) $missingFields[] = 'CIF';
                if (empty(auth()->user()->phone)) $missingFields[] = 'Teléfono';
                if (empty(auth()->user()->address)) $missingFields[] = 'Dirección';
            @endphp

            @if(count($missingFields) > 0)
                <div class="alert alert-warning shadow-sm border-0 mb-4 d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                    <div>
                        <strong>¡Perfil incompleto!</strong> Por favor, completa los siguientes campos en tu perfil: 
                        <span class="fw-bold text-dark">{{ implode(', ', $missingFields) }}</span>.
                        <a href="{{ route('profile.edit') }}" class="alert-link ms-2 text-decoration-underline">Ir a completar perfil</a>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="row g-4">
                    <div class="col-12 mb-2">
                        <h3 class="fw-bold">Bienvenido, {{ auth()->user()->name }}</h3>
                        <p class="text-muted">Panel de control de Gestión Empresarial.</p>
                    </div>

                    @if(auth()->user()->isAdmin())
                    <div class="col-md-4">
                        <div class="card h-100 border-primary shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-users text-primary mb-3" style="font-size: 2.5rem;"></i>
                                <h5 class="card-title">Empleados</h5>
                                <p class="card-text">Gestionar el personal y sus accesos.</p>
                                <a href="{{ route('employees.index') }}" class="btn btn-primary">Acceder</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card h-100 border-success shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-building text-success mb-3" style="font-size: 2.5rem;"></i>
                                <h5 class="card-title">Clientes</h5>
                                <p class="card-text">Base de datos de clientes y contratos.</p>
                                <a href="{{ route('clients.index') }}" class="btn btn-success">Acceder</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card h-100 border-warning shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-file-invoice-dollar text-warning mb-3" style="font-size: 2.5rem;"></i>
                                <h5 class="card-title">Cuotas y Facturación</h5>
                                <p class="card-text">Remesas, cuotas y facturas PDF.</p>
                                <a href="{{ route('fees.index') }}" class="btn btn-warning text-white">Acceder</a>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="col-md-{{ auth()->user()->isAdmin() ? '12' : '12' }}">
                        <div class="card h-100 border-info shadow-sm">
                            <div class="card-body text-center">
                                <i class="fas fa-tasks text-info mb-3" style="font-size: 2.5rem;"></i>
                                <h5 class="card-title">Incidencias y Tareas</h5>
                                <p class="card-text">
                                    @if(auth()->user()->isAdmin())
                                        Seguimiento global de todas las incidencias técnicas.
                                    @else
                                        Gestiona tus tareas asignadas y actualiza su estado.
                                    @endif
                                </p>
                                <a href="{{ route('tasks.index') }}" class="btn btn-info text-white">Ver Mis Tareas</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="alert alert-secondary border-0 shadow-sm">
                            <h5 class="alert-heading"><i class="fas fa-external-link-alt me-2"></i>Acceso Público para Clientes</h5>
                            <p class="mb-0">
                                Los clientes pueden registrar incidencias sin estar autenticados en el siguiente enlace:
                                <br>
                                <a href="{{ route('tasks.public.create') }}" class="fw-bold" target="_blank">
                                    {{ route('tasks.public.create') }}
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>