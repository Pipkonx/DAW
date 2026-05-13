@extends('layouts.app')

@section('title', 'Clientes API')

@section('content')
<div class="container py-4">
    <h3>Gestión de Clientes</h3>

    <table class="table table-sm table-bordered mt-3">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>CIF</th>
                <th>Moneda</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="clients-table"></tbody>
    </table>

    <div class="mt-4 border-top pt-3" id="form-container">
        <h6 id="form-title">Nuevo Cliente</h6>
        <input type="hidden" id="edit-id">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" id="name" placeholder="Nombre" class="form-control form-control-sm">
            </div>
            <div class="col-md-3">
                <input type="text" id="cif" placeholder="CIF" class="form-control form-control-sm">
            </div>
            <div class="col-md-2">
                <select id="currency" class="form-select form-select-sm">
                    <option value="EUR">EUR</option>
                    <option value="USD">USD</option>
                    <option value="GBP">GBP</option>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button id="btn-submit" onclick="saveClient()" class="btn btn-primary btn-sm w-100">Guardar</button>
                <button id="btn-cancel" onclick="resetForm()" class="btn btn-secondary btn-sm d-none">X</button>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="{{ route('cuotas.index') }}" class="btn btn-link btn-sm p-0">← Volver al Panel</a>
    </div>
</div>

<script>
    const API_URL = '/api/clientes';
    let isEditing = false;

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
                        <td class="text-center">
                            <button onclick="editClient(${client.id}, '${client.name}', '${client.cif}', '${client.currency}')" class="btn btn-warning btn-sm">Editar</button>
                            <button onclick="deleteClient(${client.id})" class="btn btn-danger btn-sm">Eliminar</button>
                        </td>
                    </tr>
                `;
            });
            loading.style.display = 'none';
        } catch (error) {
            console.error('Error cargando clientes:', error);
        }
    }

    async function saveClient() {
        const id = document.getElementById('edit-id').value;
        const name = document.getElementById('name').value;
        const cif = document.getElementById('cif').value;
        const currency = document.getElementById('currency').value;

        const method = isEditing ? 'PUT' : 'POST';
        const url = isEditing ? `${API_URL}/${id}` : API_URL;

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ name, cif, currency })
            });

            if (response.ok) {
                alert(isEditing ? 'Cliente actualizado con éxito' : 'Cliente creado con éxito');
                resetForm();
                loadClients();
            } else {
                const data = await response.json();
                let errorMsg = 'Error en la validación:\n';
                for (const field in data.errors) {
                    errorMsg += `- ${data.errors[field].join(', ')}\n`;
                }
                alert(errorMsg);
            }
        } catch (error) {
            console.error('Error al guardar:', error);
        }
    }

    function editClient(id, name, cif, currency) {
        isEditing = true;
        document.getElementById('edit-id').value = id;
        document.getElementById('name').value = name;
        document.getElementById('cif').value = cif;
        document.getElementById('currency').value = currency;
        
        document.getElementById('form-title').innerText = 'Editando ID: ' + id;
        document.getElementById('btn-submit').innerText = 'Actualizar';
        document.getElementById('btn-cancel').classList.remove('d-none');
    }

    function resetForm() {
        isEditing = false;
        document.getElementById('edit-id').value = '';
        document.getElementById('name').value = '';
        document.getElementById('cif').value = '';
        document.getElementById('currency').value = 'EUR';
        
        document.getElementById('form-title').innerText = 'Nuevo Cliente';
        document.getElementById('btn-submit').innerText = 'Guardar';
        document.getElementById('btn-cancel').classList.add('d-none');
    }

    async function deleteClient(id) {
        if (!confirm('¿Seguro que quieres eliminar este cliente?')) return;

        try {
            const response = await fetch(`${API_URL}/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (response.ok) {
                alert('Cliente eliminado');
                loadClients();
            } else {
                alert('Error al eliminar');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    loadClients();
</script>
@endsection
