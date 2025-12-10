document.addEventListener('DOMContentLoaded', () => {

    let datosPoblaciones = [];

    async function obtenerPoblaciones() {
        const params = new URLSearchParams();
        params.append('action', 'getAllPoblaciones');

        const respuesta = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: params
        });
        let datos = await respuesta.json();
        datosPoblaciones = datos;
        renderPoblacionesTable();
    }

    const entradaCp = document.getElementById('cp');
    const entradaPoblacion = document.getElementById('poblacion');
    const entradaHabitantes = document.getElementById('habitantes');
    const botonGuardarHabitantes = document.querySelector('#habitantes-section input[type="submit"]');
    const seccionGestionHabitantes = document.getElementById('habitantes-section');
    const divSugerenciasCp = document.getElementById('suggestions-cp');
    const divSugerenciasPoblacion = document.getElementById('suggestions-poblacion');
    const tablaPoblacionesBody = document.querySelector('#tablaPoblaciones tbody');

    async function renderPoblacionesTable() {
        tablaPoblacionesBody.innerHTML = '';
        if (datosPoblaciones.length === 0) {
            await obtenerPoblaciones();
        }
        datosPoblaciones.forEach(poblacion => {
            const row = tablaPoblacionesBody.insertRow();
            row.insertCell().textContent = poblacion.cp;
            row.insertCell().textContent = poblacion.nombre;
            row.insertCell().textContent = poblacion.habitantes;
            const accionesCell = row.insertCell();
            const deleteButton = document.createElement('button');
            deleteButton.textContent = 'Eliminar';
            deleteButton.addEventListener('click', () => eliminarPoblacion(poblacion.cp));
            accionesCell.appendChild(deleteButton);
        });
    }

    async function eliminarPoblacion(cp) {
        if (confirm(`¿Estás seguro de que quieres eliminar la población con CP ${cp}?`)) {
            const params = new URLSearchParams();
            params.append('action', 'deletePoblacion');
            params.append('cp', cp);

            const respuesta = await fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: params
            });
            const resultado = await respuesta.json();

            if (resultado.success) {
                alert('Población eliminada correctamente.');
                await obtenerPoblaciones(); // Volver a cargar la tabla
            } else {
                alert('Error al eliminar la población.');
            }
        }
    }

    async function mostrarSugs(inputElemento, divSugerencias, claveFiltro) {
        const valorInput = inputElemento.value.toLowerCase();
        divSugerencias.innerHTML = '';
        divSugerencias.style.display = 'none';

        if (valorInput.length === 0) {
            return;
        }

        if (datosPoblaciones.length === 0) {
            await obtenerPoblaciones();
        }

        const sugerenciasFiltradas = [];
        for (const poblacion of datosPoblaciones) {
            if (poblacion && poblacion[claveFiltro] && poblacion[claveFiltro].toLowerCase().includes(valorInput)) {
                sugerenciasFiltradas.push(poblacion);
            }
        }

        if (sugerenciasFiltradas.length > 0) {
            for (const poblacion of sugerenciasFiltradas) {
                const item = document.createElement('div');
                item.classList.add('suggestion-item');
                item.textContent = `${poblacion.cp} - ${poblacion.nombre}`;
                item.addEventListener('click', () => {
                    entradaCp.value = poblacion.cp;
                    entradaPoblacion.value = poblacion.nombre;
                    divSugerenciasCp.style.display = 'none';
                    divSugerenciasPoblacion.style.display = 'none';
                    verificarHab(poblacion.cp);
                });
                divSugerencias.appendChild(item);
            }
            divSugerencias.style.display = 'block';
        }
    }

    async function verificarHab(cp) {
        const params = new URLSearchParams();
        params.append('action', 'getPoblacionByCp');
        params.append('cp', cp);

        const respuesta = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: params
        });
        const poblacion = await respuesta.json();

        if (poblacion && poblacion.habitantes !== null) {
            entradaHabitantes.value = poblacion.habitantes;
            seccionGestionHabitantes.style.display = 'none';
        } else {
            entradaHabitantes.value = '';
            seccionGestionHabitantes.style.display = 'block';
        }
    }

    botonGuardarHabitantes.addEventListener('click', async (evento) => {
        evento.preventDefault();
        const cp = entradaCp.value;
        const habitantes = entradaHabitantes.value;

        if (cp && habitantes) {
            const params = new URLSearchParams();
            params.append('action', 'updateHabitantes');
            params.append('cp', cp);
            params.append('habitantes', habitantes);

            const respuesta = await fetch('api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: params
            });
            const resultado = await respuesta.json();

            if (resultado.success) {
                alert('Habitantes actualizados correctamente.');
                await obtenerPoblaciones(); // Volver a cargar la tabla
                verificarHab(cp);
            } else {
                alert('Error al actualizar habitantes.');
            }
        } else {
            alert('Por favor, selecciona una población e introduce el número de habitantes.');
        }
    });

    entradaCp.addEventListener('input', () => mostrarSugs(entradaCp, divSugerenciasCp, 'cp'));
    entradaPoblacion.addEventListener('input', () => mostrarSugs(entradaPoblacion, divSugerenciasPoblacion, 'nombre'));

    // Cargar la tabla de poblaciones al inicio
    obtenerPoblaciones();
});
