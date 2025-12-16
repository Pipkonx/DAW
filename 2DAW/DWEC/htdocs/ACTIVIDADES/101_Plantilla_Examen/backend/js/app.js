document.addEventListener('DOMContentLoaded', () => {
    const formularioCliente = document.getElementById('formularioCliente');
    const areaMascotas = document.getElementById('areaMascotas');
    const formularioEditarCliente = document.getElementById('formularioEditarCliente');
    const modalEditarCliente = document.getElementById('modalEditarCliente');
    const cerrarModal = document.querySelector('.cerrar-modal');
    const formularioMascota = document.getElementById('formularioMascota');
    const mascotaClienteSelect = document.getElementById('mascotaCliente');
    const filtroNombre = document.getElementById('filtroNombre');
    const filtroId = document.getElementById('filtroId');
    const btnLimpiarFiltros = document.getElementById('btnLimpiarFiltros');

    let clienteSeleccionadoId = null;



    // Función para cargar clientes en el desplegable de mascotas
    async function cargarClientes() {
        const respuesta = await fetch('./backend/api/mascotas.php?clientes=true');
        const clientes = await respuesta.json();
        mascotaClienteSelect.innerHTML = '<option value="">Seleccione un cliente</option>';
        clientes.forEach(cliente => {
            const option = document.createElement('option');
            option.value = cliente.id;
            option.textContent = `${cliente.nombre} ${cliente.apellidos}`;
            mascotaClienteSelect.appendChild(option);
        });
    }

    function renderClientesData(clientes) {
        const tbody = document.querySelector('#tablaClientes tbody');
        tbody.innerHTML = '';
        clientes.forEach(c => {
            const tr = document.createElement('tr');
            const tdId = document.createElement('td');
            tdId.textContent = c.id;
            tr.appendChild(tdId);

            const tdNombre = document.createElement('td');
            tdNombre.textContent = c.nombre;
            tr.appendChild(tdNombre);

            const tdApellidos = document.createElement('td');
            tdApellidos.textContent = c.apellidos;
            tr.appendChild(tdApellidos);

            const tdTelefono = document.createElement('td');
            tdTelefono.textContent = c.telefono;
            tr.appendChild(tdTelefono);

            const tdEmail = document.createElement('td');
            tdEmail.textContent = c.email || 'N/A';
            tr.appendChild(tdEmail);

            const tdDireccion = document.createElement('td');
            tdDireccion.textContent = c.direccion || 'N/A';
            tr.appendChild(tdDireccion);

            const tdFechaAlta = document.createElement('td');
            tdFechaAlta.textContent = c.fecha_alta;
            tr.appendChild(tdFechaAlta);

            const tdAcciones = document.createElement('td');
            tdAcciones.innerHTML = `<button class="btn-editar" data-id="${c.id}">Editar</button>
                                    <button class="btn-eliminar" data-id="${c.id}">Eliminar</button>`;
            tr.appendChild(tdAcciones);

            tbody.appendChild(tr);
        });
    }

    async function mostrarClientes() {
        const respuesta = await fetch('./backend/api/clientes.php');
        const clientes = await respuesta.json();
        renderClientesData(clientes);
    }

    async function buscarPorNombre(nombre) {
        const respuesta = await fetch(`./backend/api/clientes.php?busqueda=${encodeURIComponent(nombre)}`);
        const clientes = await respuesta.json();
        renderClientesData(clientes);
    }

    async function buscarPorId(id) {
        const respuesta = await fetch(`./backend/api/clientes.php?id=${encodeURIComponent(id)}`);
        const cliente = await respuesta.json();
        const clientes = cliente ? [cliente] : [];
        renderClientesData(clientes);
    }

    function aplicarFiltros() {
        const idVal = filtroId ? filtroId.value.trim() : '';
        const nombreVal = filtroNombre ? filtroNombre.value.trim() : '';
        if (idVal) {
            buscarPorId(idVal);
        } else if (nombreVal) {
            buscarPorNombre(nombreVal);
        } else {
            mostrarClientes();
        }
    }

    async function eliminarCli(id) {
        const respuesta = await fetch('./backend/api/clientes.php', {
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
    async function eliminarMas(id) {
        const respuesta = await fetch('./backend/api/mascotas.php', {
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
        const e = especie.toLowerCase();
        if (e === 'perro') rutaImagen = './images/perro.png';
        else if (e === 'gato' || e === 'gata') rutaImagen = './images/gato.png';
        else if (e === 'ave') rutaImagen = './images/ave.png';
        else if (e === 'reptil') rutaImagen = './images/reptil.png';
        else rutaImagen = '';
        return rutaImagen ? `<img src="${rutaImagen}" alt="${especie}" style="width: 24px; height: 24px;">` : '';
    }

    async function mostrarMas(clienteId) {
        const respuesta = await fetch(`./backend/api/mascotas.php?cliente_id=${clienteId}`);
        const mascotas = await respuesta.json();

        const tablaMascotasBody = document.querySelector('#tablaMascotas tbody');
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
            if (!datosCliente.nombre) {
                alert('El nombre es obligatorio.');
                return;
            }
            if (!datosCliente.apellidos) {
                alert('Los apellidos son obligatorios.');
                return;
            }
            if (!datosCliente.telefono) {
                alert('El teléfono es obligatorio.');
                return;
            }
            // el metodo de test es para verificar si el telefono solo contiene numeros
            if (!/^[0-9]+$/.test(datosCliente.telefono)) {
                alert('El teléfono debe contener solo números.');
                return;
            }
            if (datosCliente.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(datosCliente.email)) {
                alert('El formato del email no es válido.');
                return;
            }

            const formData = new FormData();
            for (const key in datosCliente) {
                formData.append(key, datosCliente[key]);
            }

            const respuesta = await fetch('./backend/api/clientes.php', {
                method: 'POST',
                body: formData
            });

            const resultado = await respuesta.json();

                if(respuesta.ok) {
            alert(resultado.mensaje);
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

        const respuesta = await fetch('./backend/api/mascotas.php', {
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
        if (!datosClienteEdit.nombre) {
            alert('El nombre es obligatorio.');
            return;
        }
        if (!datosClienteEdit.apellidos) {
            alert('Los apellidos son obligatorios.');
            return;
        }
        if (!datosClienteEdit.telefono) {
            alert('El teléfono es obligatorio.');
            return;
        }
        // el test es para verificar si el telefono solo contiene numeros
        if (!/^[0-9]+$/.test(datosClienteEdit.telefono)) {
            alert('El teléfono debe contener solo números.');
            return;
        }
        if (datosClienteEdit.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(datosClienteEdit.email)) {
            alert('El formato del email no es válido.');
            return;
        }

        const formData = new FormData();
        for (const key in datosClienteEdit) {
            formData.append(key, datosClienteEdit[key]);
        }

        const respuesta = await fetch('./backend/api/clientes.php', {
            method: 'POST',
            body: formData
        });

        const resultado = await respuesta.json();

        if (respuesta.ok) {
            alert(resultado.mensaje);
            modalEditarCliente.style.display = 'none'; // Cerrar modal
            mostrarClientes(); // Actualizar la lista de clientes
            cargarClientes(); // Actualizar el desplegable de clientes en el formulario de mascotas
        } else {
            alert('Error: ' + (resultado.error || 'No se pudo modificar el cliente.'));
        }
    });
}

if (cerrarModal) {
    cerrarModal.addEventListener('click', () => {
        modalEditarCliente.style.display = 'none';
    });
}

window.addEventListener('click', (evento) => {
    if (evento.target == modalEditarCliente) {
        modalEditarCliente.style.display = 'none';
    }
});

// Cargar clientes al iniciar la página
mostrarClientes();
cargarClientes();
cargarEst();

    if (filtroNombre) {
        filtroNombre.addEventListener('input', aplicarFiltros);
    }
    if (filtroId) {
        filtroId.addEventListener('input', aplicarFiltros);
    }
    if (btnLimpiarFiltros) {
        btnLimpiarFiltros.addEventListener('click', () => {
            if (filtroNombre) filtroNombre.value = '';
            if (filtroId) filtroId.value = '';
            mostrarClientes();
        });
    }

// Función para cargar las estadísticas
async function cargarEst() {
    const respuesta = await fetch('./backend/api/estadisticas.php');
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
}

// Adjuntar event listeners para editar, eliminar y mostrar mascotas una sola vez
document.querySelector('#tablaClientes tbody').addEventListener('click', async function (event) {
    if (event.target.classList.contains('btn-eliminar')) {
        const id = event.target.dataset.id;
        if (confirm('¿Estás seguro de que quieres eliminar este cliente y todas sus mascotas?')) {
            eliminarCli(id);
        }
    } else if (event.target.classList.contains('btn-editar')) {
        const id = event.target.dataset.id;
        clienteSeleccionadoId = id; // Guardar el ID del cliente seleccionado

        // Obtener los datos del cliente para rellenar el formulario de edición
        const respuesta = await fetch(`./backend/api/clientes.php?id=${id}`);
        const cliente = await respuesta.json();

        if (respuesta.ok) {
            document.getElementById('editarNombre').value = cliente.nombre;
            document.getElementById('editarApellidos').value = cliente.apellidos;
            document.getElementById('editarTelefono').value = cliente.telefono;
            document.getElementById('editarEmail').value = cliente.email;
            document.getElementById('editarDireccion').value = cliente.direccion;
            modalEditarCliente.style.display = 'block'; // Mostrar el modal
            mostrarMas(id); // Mostrar las mascotas del cliente seleccionado
        } else {
            alert('Error al cargar los datos del cliente para edición.');
        }
    }
});

document.querySelector('#tablaMascotas tbody').addEventListener('click', function (event) {
    if (event.target.classList.contains('btn-eliminar-mascota')) {
        const id = event.target.dataset.id;
        if (confirm('¿Estás seguro de que quieres eliminar esta mascota?')) {
            eliminarMas(id);
        }
    }
});
});
