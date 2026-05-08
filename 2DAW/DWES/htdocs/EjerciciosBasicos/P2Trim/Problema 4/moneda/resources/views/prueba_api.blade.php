@extends('layouts.app')

@section('title', 'Cliente API - JS')

@section('content')
<div class="main-container">
    <h1 class="h3 mb-3">Consumo de API con JavaScript (Fetch)</h1>
    <p class="text-muted mb-4 small">Este cliente consume el servicio REST de clientes mediante peticiones asíncronas.</p>

    <div id="loading" class="text-primary fw-bold mb-3">
        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
        Cargando datos...
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-dark">
                <tr>
                    <th class="ps-3">ID</th>
                    <th>Nombre</th>
                    <th>CIF</th>
                    <th class="text-center">Moneda</th>
                </tr>
            </thead>
            <tbody id="clients-table">
                <!-- Los datos se cargarán aquí -->
            </tbody>
        </table>
    </div>

    <div class="mt-5 pt-4 border-top">
        <h5 class="mb-3 fw-bold">Probar POST (Crear Cliente)</h5>
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" id="name" placeholder="Nombre" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <input type="text" id="cif" placeholder="CIF" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <input type="text" id="currency" placeholder="USD" class="form-control form-control-sm">
            </div>
            <div class="col-md-4">
                <button onclick="createClient()" class="btn btn-success btn-sm w-100 fw-bold">Añadir Cliente</button>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('cuotas.index') }}" class="btn btn-link btn-sm p-0">← Volver al Panel</a>
    </div>
</div>

<script>
    const API_URL = '/api/clientes';

    async function loadClients() {
        try {
            const response = await fetch(API_URL);
            const clients = await response.json();
            const tableBody = document.getElementById('clients-table');
            const loading = document.getElementById('loading');
            
            tableBody.innerHTML = '';
            clients.forEach(client => {
                tableBody.innerHTML += `
                    <tr>
                        <td class="ps-3">${client.id}</td>
                        <td>${client.name}</td>
                        <td>${client.cif}</td>
                        <td class="text-center fw-bold text-primary">${client.currency}</td>
                    </tr>
                `;
            });
            loading.style.display = 'none';
        } catch (error) {
            console.error('Error cargando clientes:', error);
        }
    }

    async function createClient() {
        const name = document.getElementById('name').value;
        const cif = document.getElementById('cif').value;
        const currency = document.getElementById('currency').value;

        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name, cif, currency })
            });

            if (response.ok) {
                alert('Cliente creado con éxito');
                loadClients();
                // Limpiar campos
                document.getElementById('name').value = '';
                document.getElementById('cif').value = '';
                document.getElementById('currency').value = '';
            } else {
                const data = await response.json();
                alert('Error: ' + JSON.stringify(data.errors));
            }
        } catch (error) {
            console.error('Error creando cliente:', error);
        }
    }

    loadClients();
</script>
@endsection
