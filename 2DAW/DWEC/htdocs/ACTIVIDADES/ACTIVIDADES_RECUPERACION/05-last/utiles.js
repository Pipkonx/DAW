// Esperamos a que la página esté totalmente cargada
document.addEventListener('DOMContentLoaded', () => {

    // 1. Seleccionamos los elementos del HTML que vamos a usar
    const selectorCategorias = document.getElementById('categorySelect');
    const contenedorTabla = document.getElementById('productsTableContainer');
    const selectorProductos = document.getElementById('productSelect');
    const detalleProducto = document.getElementById('selectedProductDetails');

    // Variable global para guardar los productos de la categoría seleccionada
    let productosActuales = [];

    // --- FUNCIÓN PARA CARGAR CATEGORÍAS (Se ejecuta al abrir la página) ---
    async function cargarCategorias() {
        // 'fetch' pide los datos al archivo PHP
        const respuesta = await fetch('listaCategorias.php');
        // Convertimos la respuesta en una lista de objetos de JavaScript
        const categorias = await respuesta.json();

        // Recorremos cada categoría y la añadimos al menú desplegable
        categorias.forEach(cat => {
            const opcion = document.createElement('option');
            opcion.value = cat.id_categoria;      // Lo que el código lee
            opcion.textContent = cat.nombre_categoria; // Lo que el usuario ve
            selectorCategorias.appendChild(opcion);
        });
    }

    // --- FUNCIÓN PARA CARGAR PRODUCTOS ---
    async function cargarProductos(idCategoria) {
        if (idCategoria === "") return; // Si no hay categoría, no hacemos nada

        // Pedimos los productos de esa categoría específica
        const respuesta = await fetch('productosPorCat.php?id_cat=' + idCategoria);
        productosActuales = await respuesta.json(); // Guardamos los productos

        // Dibujamos la tabla de productos
        let htmlTabla = '<table border="1"><tr><th>Nombre</th><th>Precio</th></tr>';
        productosActuales.forEach(p => {
            htmlTabla += `<tr><td>${p.nombre_producto}</td><td>${p.precio}€</td></tr>`;
        });
        htmlTabla += '</table>';
        contenedorTabla.innerHTML = htmlTabla;

        // También rellenamos el segundo desplegable
        selectorProductos.innerHTML = '<option value="">-- Selecciona un Producto --</option>';
        selectorProductos.disabled = false; // Lo activamos
        productosActuales.forEach(p => {
            const opcion = document.createElement('option');
            opcion.value = p.id_producto
            opcion.textContent = p.nombre_producto;
            selectorProductos.appendChild(opcion);
        });
    }

    // --- FUNCIÓN PARA MOSTRAR DETALLES ---

    // const p = productosActuales.find(prod => prod.id_producto == idProducto);


    function mostrarDetalle(idProducto) {
        // Buscamos el producto en nuestra lista guardada usando un bucle simple
        let p = null;
        for (let prod of productosActuales) {
            if (prod.id_producto == idProducto) {
                p = prod; // Si lo encontramos, lo guardamos en 'p'
            }
        }

        if (p) {
            detalleProducto.innerHTML = `
                <h3>Detalles: ${p.nombre_producto}</h3>
                <p>Precio: <strong>${p.precio}€</strong></p>
                <p>ID del producto: ${p.id_producto}</p>
            `;
        }
    }

    // --- CONFIGURACIÓN DE EVENTOS ---

    // Al cargar la página, pedimos las categorías
    cargarCategorias();

    // Cuando el usuario cambie la categoría en el menú
    selectorCategorias.addEventListener('change', () => {
        cargarProductos(selectorCategorias.value);
    });

    // Cuando el usuario elija un producto específico
    selectorProductos.addEventListener('change', () => {
        mostrarDetalle(selectorProductos.value);
    });

});
