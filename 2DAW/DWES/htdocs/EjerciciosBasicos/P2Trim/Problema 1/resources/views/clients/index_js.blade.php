<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Clientes (JS + DataTables)') }}
        </h2>
    </x-slot>

    <!-- CSS de DataTables desde CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                
                <h3>Gestión Rápida de Clientes (Sin Recargar)</h3>
                <p class="text-muted">Este apartado usa JavaScript (Fetch API) para comunicarse con el servidor.</p>

                <!-- Botón para abrir un formulario sencillo (podría ser un modal) -->
                <div class="mb-4">
                    <button id="btnNuevo" class="btn btn-primary">Nuevo Cliente</button>
                </div>

                <!-- Tabla donde se cargarán los datos -->
                <table id="tablaClientes" class="display" style="width:100%">
                    <thead>
                        <tr>
                            <th>CIF</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoTabla">
                        <!-- Se llena con JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Scripts necesarios: jQuery y DataTables (CDN) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>
        // Cuando el documento esté listo...
        $(document).ready(function() {
            // Función para cargar los datos de la API
            function cargarClientes() {
                // Fetch es la forma moderna de hacer peticiones JS
                fetch('/api/clients')
                    .then(response => response.json())
                    .then(data => {
                        let html = '';
                        // Recorremos los clientes y creamos las filas
                        data.forEach(cliente => {
                            html += `
                                <tr>
                                    <td>${cliente.cif}</td>
                                    <td>${cliente.name}</td>
                                    <td>${cliente.phone}</td>
                                    <td>${cliente.email}</td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" onclick="eliminarCliente(${cliente.id})">Borrar</button>
                                    </td>
                                </tr>
                            `;
                        });
                        // Metemos el HTML en la tabla
                        $('#cuerpoTabla').html(html);
                        // Convertimos la tabla normal en una DataTable con buscador y paginación
                        $('#tablaClientes').DataTable();
                    });
            }

            // Llamamos a la función al cargar la página
            cargarClientes();

            // Función global para que el botón "Borrar" funcione
            window.eliminarCliente = function(id) {
                if(confirm('¿Seguro que quieres borrarlo?')) {
                    fetch('/api/clients/' + id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Laravel necesita el token para seguridad
                        }
                    })
                    .then(r => r.json())
                    .then(res => {
                        if(res.success) {
                            alert('Borrado con éxito');
                            location.reload(); // Recargamos para ver el cambio (o podrías quitar la fila con JS)
                        }
                    });
                }
            };
        });
    </script>
</x-app-layout>
