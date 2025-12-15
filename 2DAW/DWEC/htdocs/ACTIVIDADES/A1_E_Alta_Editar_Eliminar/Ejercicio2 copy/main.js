
let articulos = [];

async function fetchArticulos() {
    try {
        const response = await fetch('backend/api.php');
        articulos = await response.json();
        cargarArticulosEnTabla(); // Initial load of articles
    } catch (error) {
        console.error('Error fetching articles:', error);
    }
}

fetchArticulos();

const campoCodigo = document.getElementById('codigo');
const campoDescripcion = document.getElementById('descripcion');
const campoCantidad = document.getElementById('cantidad');
const campoPrecio = document.getElementById('precio');
const campoSubtotal = document.getElementById('subtotal');
const botonInsertar = document.getElementById('insertar');
const tablaFacturaBody = document.querySelector('#tablaFactura tbody');
const campoBase = document.getElementById('base');
const campoTotal = document.getElementById('total');

const btnBuscarArticulos = document.getElementById('btnBuscarArticulos');
const capaArticulos = document.getElementById('capaArticulos');
const tablaArticulosBody = document.querySelector('#tablaArticulos tbody');
const cerrarCapaArticulos = document.getElementById('cerrarCapaArticulos');
const buscadorArticulos = document.getElementById('buscadorArticulos');

let totalBase = 0;
const IVA = 0.21;

function buscarArticulo(codigo) {
    let articuloEncontrado = null;
    for (let i = 0; i < articulos.length; i++) {
        if (articulos[i].codigo === codigo) {
            articuloEncontrado = articulos[i];
            break;
        }
    }
    return articuloEncontrado;
}

function calcularSubtotal() {
    const cantidad = parseFloat(campoCantidad.value);
    const precio = parseFloat(campoPrecio.value);
    if (!isNaN(cantidad) && !isNaN(precio)) {
        campoSubtotal.value = (cantidad * precio).toFixed(2);
    } else {
        campoSubtotal.value = '';
    }
}

function actualizarTotales() {
    totalBase = 0;
    const filas = tablaFacturaBody.querySelectorAll('tr');
    filas.forEach(fila => {
        const subtotalCelda = fila.children[4].textContent;
        totalBase += parseFloat(subtotalCelda);
    });
    campoBase.value = totalBase.toFixed(2);
    campoTotal.value = (totalBase * (1 + IVA)).toFixed(2);
}

function cargarArticulosEnTabla(filtro = '') {
    tablaArticulosBody.innerHTML = '';
    const filtroLower = filtro.toLowerCase();
    for (let i = 0; i < articulos.length; i++) {
        const articulo = articulos[i];
        if (articulo.descripcion.toLowerCase().indexOf(filtroLower) !== -1) {
            const fila = tablaArticulosBody.insertRow();
            fila.insertCell().textContent = articulo.codigo;
            fila.insertCell().textContent = articulo.descripcion;
            fila.insertCell().textContent = articulo.precio.toFixed(2);
            fila.style.cursor = 'pointer';
            fila.addEventListener('click', () => {
                campoCodigo.value = articulo.codigo;
                campoDescripcion.value = articulo.descripcion;
                campoPrecio.value = articulo.precio.toFixed(2);
                campoCantidad.value = 1;
                calcularSubtotal();
                capaArticulos.style.display = 'none';
            });
        }
    }
}

campoCodigo.addEventListener('input', () => {
    const articulo = buscarArticulo(campoCodigo.value);
    if (articulo) {
        campoDescripcion.value = articulo.descripcion;
        campoPrecio.value = articulo.precio.toFixed(2);
        campoCantidad.value = 1;
    } else {
        campoDescripcion.value = '';
        campoPrecio.value = '';
        campoCantidad.value = 1;
    }
    calcularSubtotal();
});

campoCantidad.addEventListener('input', calcularSubtotal);

botonInsertar.addEventListener('click', () => {
    const codigo = campoCodigo.value;
    const descripcion = campoDescripcion.value;
    const cantidad = parseFloat(campoCantidad.value);
    const precio = parseFloat(campoPrecio.value);
    const subtotal = parseFloat(campoSubtotal.value);

    if (codigo && descripcion && !isNaN(cantidad) && !isNaN(precio) && !isNaN(subtotal) && cantidad > 0) {
        const nuevaFila = tablaFacturaBody.insertRow();
        nuevaFila.insertCell().textContent = codigo;
        nuevaFila.insertCell().textContent = descripcion;
        nuevaFila.insertCell().textContent = cantidad;
        nuevaFila.insertCell().textContent = precio.toFixed(2);
        nuevaFila.insertCell().textContent = subtotal.toFixed(2);

        actualizarTotales();

        // Limpiar formulario
        campoCodigo.value = '';
        campoDescripcion.value = '';
        campoCantidad.value = 1;
        campoPrecio.value = '';
        campoSubtotal.value = '';
    } else {
        alert('Por favor, complete todos los campos correctamente.');
    }
});

btnBuscarArticulos.addEventListener('click', () => {
    capaArticulos.style.display = 'block';
    cargarArticulosEnTabla();
});

cerrarCapaArticulos.addEventListener('click', () => {
    capaArticulos.style.display = 'none';
});

buscadorArticulos.addEventListener('input', (e) => {
    cargarArticulosEnTabla(e.target.value);
});

