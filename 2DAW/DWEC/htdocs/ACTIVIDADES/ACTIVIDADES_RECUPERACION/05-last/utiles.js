document.addEventListener('DOMContentLoaded', () => {
    // 1. Guardamos los elementos de la pantalla en variables
    const comboCategorias = document.getElementById('categorySelect');
    const divTabla = document.getElementById('productsTableContainer');
    const comboProductos = document.getElementById('productSelect');
    const divDetalle = document.getElementById('selectedProductDetails');

    // Variable para guardar los productos que descargamos
    let listaDeProductos = [];

    // --- FUNCIÓN PARA CARGAR LAS CATEGORÍAS AL EMPEZAR ---
    async function cargarCategorias() {
        // Pedimos los datos al fichero PHP
        const respuesta = await fetch('listaCategorias.php');
        const datos = await respuesta.json();

        // Limpiamos el desplegable y ponemos la opción por defecto
        comboCategorias.innerHTML = '<option value="">-- Selecciona Categoría --</option>';

        // Recorremos las categorías una a una y las añadimos al HTML
        for (let cat of datos) {
            comboCategorias.innerHTML += `<option value="${cat.id_categoria}">${cat.nombre_categoria}</option>`;
        }
    }

    // --- FUNCIÓN PARA CARGAR LOS PRODUCTOS DE UNA CATEGORÍA ---
    async function cargarProductos(id) {
        // Si no hay ID (opción vacía), limpiamos todo y salimos
        if (id == "") {
            divTabla.innerHTML = "Selecciona una categoría";
            return;
        }

        divTabla.innerHTML = "Cargando...";

        // Pedimos los productos de esa categoría al PHP
        const respuesta = await fetch('productosPorCat.php?id_cat=' + id);
        listaDeProductos = await respuesta.json();

        // Si no hay productos, avisamos
        if (listaDeProductos.length == 0) {
            divTabla.innerHTML = "No hay productos en esta categoría";
            return;
        }

        // --- DIBUJAMOS LA TABLA ---
        let contenidoTabla = `
            <table border="1" style="width:100%">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                </tr>`;

        // Añadimos cada producto a la tabla
        for (let p of listaDeProductos) {
            contenidoTabla += `
                <tr>
                    <td>${p.id_producto}</td>
                    <td>${p.nombre_producto}</td>
                    <td>${p.precio}€</td>
                </tr>`;
        }

        contenidoTabla += `</table>`;
        divTabla.innerHTML = contenidoTabla;

        // --- RELLENAMOS EL SEGUNDO DESPLEGABLE ---
        comboProductos.innerHTML = '<option value="">-- Selecciona un Producto --</option>';
        comboProductos.disabled = false;

        for (let p of listaDeProductos) {
            comboProductos.innerHTML += `<option value="${p.id_producto}">${p.nombre_producto}</option>`;
        }
    }

    // --- FUNCIÓN PARA MOSTRAR LOS DETALLES DE UN PRODUCTO ---
    function mostrarDetalle(idBuscado) {
        if (idBuscado == "") {
            divDetalle.innerHTML = "Detalles aquí";
            return;
        }

        // Buscamos el producto en nuestra lista
        let productoEncontrado = null;
        for (let p of listaDeProductos) {
            if (p.id_producto == idBuscado) {
                productoEncontrado = p;
            }
        }

        // Si lo encontramos, lo dibujamos
        if (productoEncontrado != null) {
            divDetalle.innerHTML = `
                <h3>${productoEncontrado.nombre_producto}</h3>
                <p>ID: ${productoEncontrado.id_producto}</p>
                <p>Precio: ${productoEncontrado.precio}€</p>
            `;
        }
    }

    // --- INICIO Y EVENTOS (CLICKS Y CAMBIOS) ---

    // Al cargar la página, cargamos las categorías
    cargarCategorias();

    // Cuando cambie el desplegable de categorías
    comboCategorias.addEventListener('change', () => {
        cargarProductos(comboCategorias.value);
    });

    // Cuando cambie el desplegable de productos
    comboProductos.addEventListener('change', () => {
        mostrarDetalle(comboProductos.value);
    });
});
