    // Función de validación de datos del cliente
    function validarDatosCliente(datosCliente) {
        if (!datosCliente.nombre) {
            alert('El nombre es obligatorio.');
            return false;
        }
        if (!datosCliente.apellidos) {
            alert('Los apellidos son obligatorios.');
            return false;
        }
        if (!datosCliente.telefono) {
            alert('El teléfono es obligatorio.');
            return false;
        }
        if (!/^[0-9]+$/.test(datosCliente.telefono)) {
            alert('El teléfono debe contener solo números.');
            return false;
        }
        if (datosCliente.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(datosCliente.email)) {
            alert('El formato del email no es válido.');
            return false;
        }
        return true;
    }

document.addEventListener('DOMContentLoaded', () => {
    const formularioCliente = document.getElementById('formulario-cliente');
    const areaMascotas = document.getElementById('area-mascotas');
    const formularioEditarCliente = document.getElementById('formulario-editar-cliente');

    const formularioMascota = document.getElementById('formulario-mascota');
    const mascotaClienteSelect = document.getElementById('mascota-cliente');

    let clienteSeleccionadoId = null;



    // Función para cargar clientes en el desplegable de mascotas
    async function cargarClientes() {
        const respuesta = await fetch('./api/mascotas.php?clientes=true');
        const clientes = await respuesta.json();
        mascotaClienteSelect.innerHTML = '<option value="">Seleccione un cliente</option>';
        clientes.forEach(cliente => {
            const option = document.createElement('option');
            option.value = cliente.id;
            option.textContent = `${cliente.nombre} ${cliente.apellidos}`;
            mascotaClienteSelect.appendChild(option);
        });
    }

    let tablaClientes = null; // Variable para almacenar lo de la DataTables

    // async function obtenerYMostrar`Cliente`s() {
    async function mostrarClientes() {
        const respuesta = await fetch('./api/clientes.php');
        const clientes = await respuesta.json();

        if (tablaClientes) {
            tablaClientes.destroy();
        }

    // Configuración de DataTables para la tabla de clientes
        tablaClientes = $('#tabla-clientes').DataTable({
            data: clientes,
            columns: [
                { data: 'id' },
                { data: 'nombre' },
                { data: 'apellidos' },
                { data: 'telefono' },
                { data: 'email', defaultContent: 'N/A' },
                { data: 'direccion', defaultContent: 'N/A' },
                { data: 'fecha_alta' },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `<button class="btn-editar" data-id="${row.id}">Editar</button>
                                <button class="btn-eliminar" data-id="${row.id}">Eliminar</button>`;
                    }
                }
            ],
            language: {
                url: './js/Spanish.json'
            },
            paging: true, // Habilitar paginación
            lengthMenu: [10, 25, 50, 75, 100], // Opciones de número de registros por página
            pageLength: 10 // Número de registros por página por defecto
        });

    }

    async function eliminarCliente(id) {
        const respuesta = await fetch('./api/clientes.php', {
            method: 'POST',
            body: `id=${id}&_method=DELETE`,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
        const resultado = await respuesta.json();
        // esto es para actualizar la tabla de clientes
        if (respuesta.ok) {
            alert(resultado.mensaje);
            mostrarClientes();
        } else {
            alert('Error: ' + (resultado.error || 'No se pudo eliminar el cliente.'));
        }
    }

    // Función para eliminar una mascota
    async function eliminarMascota(id) {
        const respuesta = await fetch('./api/mascotas.php', {
            method: 'POST',
            body: `id=${id}&_method=DELETE`,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        });
        const resultado = await respuesta.json();
        if (respuesta.ok) {
            alert(resultado.mensaje);
            // Después de eliminar una mascota, necesitamos recargar las mascotas del cliente actualmente seleccionado
            if (clienteSeleccionadoId) {
                mostrarMas(clienteSeleccionadoId);
            }
        } else {
            alert('Error: ' + (resultado.error || 'No se pudo eliminar la mascota.'));
        }
    }
    let tablaMascotas = null; // Variable para almacenar la instancia de DataTables de mascotas

    // Función auxiliar para obtener el icono de la especie
    function obtenerIconoEspecie(especie) {
        let rutaImagen = '';
        switch (especie.toLowerCase()) {
            case 'perro':
                rutaImagen = './images/perro.png';
                break;
            case 'gato':
                rutaImagen = './images/gato.png';
                break;
            case 'ave':
                rutaImagen = './images/ave.png';
                break;
            case 'reptil':
                rutaImagen = './images/reptil.png';
                break;
            default:
                rutaImagen = './images/default.png'; // Imagen por defecto
        }
        return `<img src="${rutaImagen}" alt="${especie}" style="width: 24px; height: 24px;">`;
    }

    async function mostrarMas(clienteId) {
        const respuesta = await fetch(`./api/mascotas.php?cliente_id=${clienteId}`);
        const mascotas = await respuesta.json();

        const tablaMascotasBody = document.querySelector('#tabla-mascotas tbody');
        tablaMascotasBody.innerHTML = '';

        mascotas.forEach(mascota => {
            const row = tablaMascotasBody.insertRow();
            row.insertCell().textContent = mascota.id;
            row.insertCell().textContent = mascota.nombre;
            row.insertCell().textContent = mascota.especie;
            row.insertCell().textContent = mascota.raza || 'N/A';
            row.insertCell().textContent = mascota.fecha_nacimiento || 'N/A';
            const iconoCell = row.insertCell(); // Nueva celda para el icono
            iconoCell.innerHTML = obtenerIconoEspecie(mascota.especie);
            const actionsCell = row.insertCell();
            actionsCell.innerHTML = `<button class="btn-eliminar-mascota" data-id="${mascota.id}">Eliminar</button>`;
        });
    }

    if (formularioCliente) {
        formularioCliente.addEventListener('submit', async (evento) => {
            evento.preventDefault();

            const datosCliente = {
                nombre: document.getElementById('nombre').value,
                apellidos: document.getElementById('apellidos').value,
                telefono: document.getElementById('telefono').value,
                email: document.getElementById('email').value,
                direccion: document.getElementById('direccion').value
            };

            // Validaciones
            if (!validarDatosCliente(datosCliente)) {
                return;
            }

            const formData = new FormData();
            for (const key in datosCliente) {
                formData.append(key, datosCliente[key]);
            }

            const respuesta = await fetch('./api/clientes.php', {
                method: 'POST',
                body: formData
            });

            const resultado = await respuesta.json();

                if(respuesta.ok) {
            alert(resultado.mensaje);
            formularioEditarCliente.style.display = 'none';
            document.getElementById('tituloMascotasCliente').style.display = 'none';
            document.getElementById('tablaMascotas').style.display = 'none'; // Ocultar el formulario de edición
            mostrarClientes(); // Actualizar la lista de clientes
            cargarClientes(); // Actualizar el desplegable de clientes en el formulario de mascotas
        } else {
            alert('Error: ' + (resultado.error || 'No se pudo crear el cliente.'));
        }
    });
    }

// Manejar el envío del formulario de creación de mascotas
if (formularioMascota) {
    formularioMascota.addEventListener('submit', async (evento) => {
        evento.preventDefault();

        const datosMascota = {
            nombre: document.getElementById('mascotaNombre').value,
            especie: document.getElementById('mascotaEspecie').value,
            raza: document.getElementById('mascotaRaza').value,
            fecha_nacimiento: document.getElementById('mascotaFechaNacimiento').value,
            id_cliente: document.getElementById('mascotaCliente').value
        };

        // Validaciones
        if (!datosMascota.nombre) {
            alert('El nombre de la mascota es obligatorio.');
            return;
        }
        if (!datosMascota.especie) {
            alert('La especie de la mascota es obligatoria.');
            return;
        }
        if (!datosMascota.id_cliente) {
            alert('Debe asignar un cliente a la mascota.');
            return;
        }

        const formData = new FormData();
        for (const key in datosMascota) {
            formData.append(key, datosMascota[key]);
        }

        const respuesta = await fetch('./api/mascotas.php', {
            method: 'POST',
            body: formData
        });

        const resultado = await respuesta.json();

        if (respuesta.ok) {
            alert(resultado.mensaje);
            formularioMascota.reset();
            mostrarClientes(); // Para actualizar la lista de clientes y sus mascotas si se muestra alguna
            cargarClientes(); // Actualizar el desplegable de clientes en el formulario de mascotas
        } else {
            alert('Error: ' + (resultado.error || 'No se pudo crear la mascota.'));
        }
    });
}

// Manejar el envío del formulario de edición
if (formularioEditarCliente) {
    formularioEditarCliente.addEventListener('submit', async (evento) => {
        evento.preventDefault();

        const datosClienteEdit = {
            id: clienteSeleccionadoId,
            nombre: document.getElementById('editarNombre').value,
            apellidos: document.getElementById('editarApellidos').value,
            telefono: document.getElementById('editarTelefono').value,
            email: document.getElementById('editarEmail').value,
            direccion: document.getElementById('editarDireccion').value,
            _method: 'PUT'
        };

        // Validaciones
        if (!validarDatosCliente(datosClienteEdit)) {
            return;
        }

        const formData = new FormData();
        for (const key in datosClienteEdit) {
            formData.append(key, datosClienteEdit[key]);
        }

        const respuesta = await fetch('./api/clientes.php', {
            method: 'POST',
            body: formData
        });

        const resultado = await respuesta.json();

        if (respuesta.ok) {
            alert(resultado.mensaje);

            mostrarClientes(); // Actualizar la lista de clientes
            cargarClientes(); // Actualizar el desplegable de clientes en el formulario de mascotas
        } else {
            alert('Error: ' + (resultado.error || 'No se pudo modificar el cliente.'));
        }
    });

    document.querySelector('.cerrar-formulario-edicion').addEventListener('click', function() {
        formularioEditarCliente.style.display = 'none';
        document.getElementById('tituloMascotasCliente').style.display = 'none';
        document.getElementById('tablaMascotas').style.display = 'none';
    });
}



window.addEventListener('click', (evento) => {

});

// Cargar clientes al iniciar la página
mostrarClientes();
cargarClientes();
cargarEst();

// Función para cargar las estadísticas
async function cargarEst() {
    const respuesta = await fetch('./api/estadisticas.php');
    try {
        const estadisticas = await respuesta.json();
        console.log('Estadísticas recibidas:', estadisticas); // Añadido para depuración

        if (respuesta.ok) {
            document.getElementById('totalClientes').textContent = estadisticas.total_clientes;
            document.getElementById('totalMascotas').textContent = estadisticas.total_mascotas;
            document.getElementById('especieMasComun').textContent = estadisticas.especie_mas_comun;
            document.getElementById('clienteMasMascotas').textContent = estadisticas.cliente_con_mas_mascotas;
        } else {
            console.error('Error al cargar estadísticas:', estadisticas.error);
        }
    } catch (e) {
        console.error('Error al parsear JSON de estadísticas:', e);
        const text = await respuesta.text();
        console.error('Respuesta de texto cruda:', text);
    }
}

// Adjuntar event listeners para editar, eliminar y mostrar mascotas una sola vez
document.querySelector('#tablaClientes tbody').addEventListener('click', async function (event) {
    if (event.target.classList.contains('btn-eliminar')) {
        const id = event.target.dataset.id;
        if (confirm('¿Estás seguro de que quieres eliminar este cliente y todas sus mascotas?')) {
            eliminarCliente(id);
        }
    } else if (event.target.classList.contains('btn-editar')) {
        const id = event.target.dataset.id;
        clienteSeleccionadoId = id; // Guardar el ID del cliente seleccionado

        // Obtener los datos del cliente para rellenar el formulario de edición
        const respuesta = await fetch(`./api/clientes.php?id=${id}`);
        const cliente = await respuesta.json();

        if (respuesta.ok) {
            document.getElementById('editarNombre').value = cliente.nombre;
            document.getElementById('editarApellidos').value = cliente.apellidos;
            document.getElementById('editarTelefono').value = cliente.telefono;
            document.getElementById('editarEmail').value = cliente.email;
            document.getElementById('editarDireccion').value = cliente.direccion;

            formularioEditarCliente.style.display = 'block'; // Mostrar el formulario de edición
            document.getElementById('tituloMascotasCliente').style.display = 'block';
            document.getElementById('tablaMascotas').style.display = 'block';
            mostrarMas(clienteSeleccionadoId); // Mostrar las mascotas del cliente seleccionado
        } else {
            alert('Error al cargar los datos del cliente para edición.');
        }
    }
});

document.querySelector('#tablaMascotas tbody').addEventListener('click', function (event) {
    if (event.target.classList.contains('btn-eliminar-mascota')) {
        const id = event.target.dataset.id;
        if (confirm('¿Estás seguro de que quieres eliminar esta mascota?')) {
            eliminarMascota(id);
        }
    }
});
});