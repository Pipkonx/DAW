// Referencias a los elementos del HTML
const formulario = document.getElementById('formCategoria');
const cuerpoTabla = document.getElementById('lista');
const inputNombre = document.getElementById('nombre');
const inputId = document.getElementById('id_editar');
const botonAccion = document.getElementById('btnAccion');

// 1. FUNCIÓN PARA CARGAR Y MOSTRAR CATEGORÍAS
async function cargarCategorias() {
    const respuesta = await fetch('api.php');
    const datos = await respuesta.json();

    cuerpoTabla.innerHTML = "";

    datos.forEach(cat => {
        const fila = document.createElement('tr');
        
        // Al hacer click en la fila, cargamos los datos para editar
        fila.onclick = () => prepararEdicion(cat, fila);

        fila.innerHTML = `
            <td>${cat.id}</td>
            <td>${cat.nombre}</td>
            <td>
                <button onclick="event.stopPropagation(); eliminar(${cat.id})">Eliminar</button>
            </td>
        `;
        cuerpoTabla.appendChild(fila);
    });
}

// 2. FUNCIÓN PARA PREPARAR LA EDICIÓN (AL HACER CLICK EN LA FILA)
function prepararEdicion(categoria, fila) {
    // 1. Quitamos el color de cualquier otra fila seleccionada anteriormente
    const filas = document.querySelectorAll('tr');
    filas.forEach(f => f.style.backgroundColor = "");

    // 2. Ponemos color a la fila actual
    fila.style.backgroundColor = "#e0f7fa"; // Un azul clarito

    // 3. Cargamos los datos en el formulario
    inputNombre.value = categoria.nombre;
    inputId.value = categoria.id;

    // 4. Cambiamos el texto del botón
    botonAccion.textContent = "Guardar cambios";
}

// 3. FUNCIÓN PARA AGREGAR O MODIFICAR
formulario.addEventListener('submit', async (e) => {
    e.preventDefault();

    const nombre = inputNombre.value;
    const id = inputId.value;

    if (id === "") {
        // MODO AGREGAR (POST)
        await fetch('api.php', {
            method: 'POST',
            body: JSON.stringify({ nombre: nombre })
        });
    } else {
        // MODO MODIFICAR (PUT)
        await fetch('api.php', {
            method: 'PUT',
            body: JSON.stringify({ id: id, nombre: nombre })
        });
    }

    // Resetear formulario y recargar
    resetearFormulario();
    cargarCategorias();
});

// 4. FUNCIÓN PARA ELIMINAR
async function eliminar(id) {
    await fetch('api.php?id=' + id, {
        method: 'DELETE'
    });
    cargarCategorias();
}

// FUNCIÓN PARA LIMPIAR EL FORMULARIO
function resetearFormulario() {
    inputNombre.value = "";
    inputId.value = "";
    botonAccion.textContent = "Agregar";
    
    // Quitar colores de las filas
    const filas = document.querySelectorAll('tr');
    filas.forEach(f => f.style.backgroundColor = "");
}

// Al abrir la página, cargamos las categorías
cargarCategorias();