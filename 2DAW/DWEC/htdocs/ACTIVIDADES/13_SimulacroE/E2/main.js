const articulos = [
    { codigo: 'A01', descripcion: 'Teclado', precio: 25.50 },
    { codigo: 'A02', descripcion: 'Ratón', precio: 15.20 },
    { codigo: 'A03', descripcion: 'Monitor 24"', precio: 135.99 },
    { codigo: 'A04', descripcion: 'Impresora', precio: 89.90 },
    { codigo: 'A05', descripcion: 'Altavoces', precio: 45.00 }
];

// --- a) Mostrar descripción y precio al introducir código ---
const codigoInput = document.getElementById('codigo');
const descripcionInput = document.getElementById('descripcion');
const cantidadInput = document.getElementById('cantidad');
const precioInput = document.getElementById('precio');
const subtotalInput = document.getElementById('subtotal');
const insertarBtn = document.getElementById('insertarBtn');

codigoInput.addEventListener('change', () => {
    //find es para 
    const art = articulos.find(a => a.codigo.toUpperCase() === codigoInput.value.toUpperCase());
    if (art) {
        descripcionInput.value = art.descripcion;
        precioInput.value = art.precio;
        calcularSubtotal();
    } else {
        descripcionInput.value = '';
        precioInput.value = '';
        subtotalInput.value = '';
    }
});

cantidadInput.addEventListener('input', calcularSubtotal);

function calcularSubtotal() {
    const cantidad = parseFloat(cantidadInput.value) || 0;
    const precio = parseFloat(precioInput.value) || 0;
    subtotalInput.value = (cantidad * precio).toFixed(2);
}

// --- b) Insertar línea y calcular totales ---
insertarBtn.addEventListener('click', () => {
    const tbody = document.querySelector('#tablaDetalle tbody');
    if (!codigoInput.value || !descripcionInput.value) return alert("Introduce un código válido");

    const fila = document.createElement('tr');
    fila.innerHTML = `
        <td>${codigoInput.value}</td>
        <td>${descripcionInput.value}</td>
        <td>${cantidadInput.value}</td>
        <td>${parseFloat(precioInput.value).toFixed(2)}</td>
        <td>${subtotalInput.value}</td>
    `;
    tbody.appendChild(fila);

    actualizarTotales();

    // limpiar
    codigoInput.value = '';
    descripcionInput.value = '';
    cantidadInput.value = 1;
    precioInput.value = '';
    subtotalInput.value = '';
});

function actualizarTotales() {
    let base = 0;
    document.querySelectorAll('#tablaDetalle tbody tr').forEach(tr => {
        base += parseFloat(tr.children[4].textContent);
    });
    const iva = base * 0.21;
    const total = base + iva;

    document.getElementById('base').textContent = base.toFixed(2);
    document.getElementById('iva').textContent = iva.toFixed(2);
    document.getElementById('total').textContent = total.toFixed(2);
}

// --- c) Mostrar tabla de artículos ---
const buscarBtn = document.getElementById('buscarBtn');
const tablaArticulos = document.getElementById('tablaArticulos');
const listaArticulos = document.getElementById('listaArticulos');

buscarBtn.addEventListener('click', () => {
    tablaArticulos.style.display = 'block';
    mostrarArticulos(articulos);
});

function mostrarArticulos(datos) {
    listaArticulos.innerHTML = '';
    datos.forEach(a => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>${a.codigo}</td>
            <td>${a.descripcion}</td>
            <td>${a.precio.toFixed(2)}</td>
            <td><button onclick="seleccionarArticulo('${a.codigo}')">✔</button></td>
        `;
        listaArticulos.appendChild(fila);
    });
}

window.seleccionarArticulo = function (cod) {
    const art = articulos.find(a => a.codigo === cod);
    if (art) {
        codigoInput.value = art.codigo;
        descripcionInput.value = art.descripcion;
        precioInput.value = art.precio;
        calcularSubtotal();
    }
    tablaArticulos.style.display = 'none';
};

// --- d) Buscador dinámico ---
const buscador = document.getElementById('buscador');
buscador.addEventListener('input', () => {
    const texto = buscador.value.toLowerCase();
    //includes es para verificar si la cadena de texto contiene la subcadena especificada
    const filtrados = articulos.filter(a => a.descripcion.toLowerCase().includes(texto));
    mostrarArticulos(filtrados);
});