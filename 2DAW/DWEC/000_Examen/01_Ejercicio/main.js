const tareas = [];

const formTarea = document.getElementById('formTarea');
const nombreInput = document.getElementById('nombreTarea');
const prioridadSelect = document.getElementById('prioridadTarea');
const filtroSelect = document.getElementById('filtroPrioridad');
const ulTareas = document.getElementById('listaTareas');
const tablaTareasBody = document.querySelector('#tablaTareas tbody');
const mensajeDiv = document.getElementById('mensaje');

function mostrarMensaje(texto, tipo = 'ok') {
    mensajeDiv.textContent = texto;
    mensajeDiv.classList.remove('error');
    if (tipo === 'error') mensajeDiv.classList.add('error');
}

function filtrarTareas() {
    const filtro = filtroSelect.value;
    const resultado = [];
    for (let i = 0; i < tareas.length; i++) {
        if (filtro === 'Todas' || tareas[i].prioridad === filtro) {
            resultado.push({ task: tareas[i], index: i });
        }
    }
    return resultado;
}

function listar() {
    const lista = filtrarTareas();
    ulTareas.innerHTML = '';
    for (let i = 0; i < lista.length; i++) {
        const li = document.createElement('li');
        li.textContent = lista[i].task.nombre + ' - ' + lista[i].task.prioridad + " - ";
        const btn = document.createElement('button');
        btn.textContent = 'Eliminar';
        btn.dataset.index = lista[i].index;
        btn.onclick = function () {
            eliminarTarea(lista[i].index);
        };
        li.appendChild(btn);
        ulTareas.appendChild(li);
    }
}

function tabla() {
    const lista = filtrarTareas();
    tablaTareasBody.innerHTML = '';
    for (let i = 0; i < lista.length; i++) {
        const tr = document.createElement('tr');

        const tdNombre = document.createElement('td');
        tdNombre.textContent = lista[i].task.nombre;
        tr.appendChild(tdNombre);

        const tdPrioridad = document.createElement('td');
        tdPrioridad.textContent = lista[i].task.prioridad;
        tr.appendChild(tdPrioridad);

        const tdAccion = document.createElement('td');
        const btn = document.createElement('button');
        btn.textContent = 'Eliminar';
        btn.dataset.index = lista[i].index;
        btn.onclick = function () {
            eliminarTarea(lista[i].index);
        };
        tdAccion.appendChild(btn);
        tr.appendChild(tdAccion);

        tablaTareasBody.appendChild(tr);
    }
}

function eliminarTarea(index) {
    if (index >= 0) {
        // Cambia el contenido de un array eliminando elementos existentes y / o agregando nuevos elementos.
        tareas.splice(index, 1);
        mostrarMensaje('Tarea eliminada');

        listar();
        tabla();
    }
}

function actualizarTarea(nombre, prioridad) {
    const nombreNorm = nombre;
    if (nombreNorm === '') {
        mostrarMensaje('Introduce un nombre de tarea válido ', 'error');
        return;
    }

    let existe = -1;
    for (let i = 0; i < tareas.length; i++) {
        if (tareas[i].nombre.toLowerCase() === nombreNorm.toLowerCase()) {
            existe = i;
            break;
        }
    }

    if (existe >= 0) {
        if (tareas[existe].prioridad !== prioridad) {
            tareas[existe].prioridad = prioridad;
            mostrarMensaje('Prioridad actualizada');
        } else {
            mostrarMensaje('La tarea ya existe ', 'error');
            return;
        }
    } else {
        tareas.push({ nombre: nombreNorm, prioridad: prioridad });
        mostrarMensaje('Tarea añadida');
    }
    listar();
    tabla();
}

formTarea.addEventListener('submit', function (e) {
    //para si se puede
    e.preventDefault();
    const nombre = nombreInput.value;
    const prioridad = prioridadSelect.value;
    actualizarTarea(nombre, prioridad);
    formTarea.reset();
    nombreInput.focus();
});

filtroSelect.addEventListener('change', function () {
    listar();
    tabla();
});
listar();
tabla();