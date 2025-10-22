let productosData = {};
let productosStock = {};
let cart = [];

function generarTarjetaProducto(producto) {
    const stockActual = productosStock[producto.id] ?? producto.rating.count;
    return `
    <img src="${producto.image}" /> 
    <h3 class="card-title">${producto.title}</h3>
    <p class="precio">${producto.price}€</p>
    <p class="description">${producto.description}</p>
    <section class="meta">
        <span class="catRopa">${producto.category}</span>
        <span class="rating-badge">⭐ ${producto.rating.rate}</span>
    </section>
    <p>Stock: <span id="stock-${producto.id}">${stockActual}</span></p>
    <section class="actions">
        <label class="cantidad-label" for="cantidad-${producto.id}">Cantidad</label>
        <input type="number" id="cantidad-${producto.id}" class="cantidad" value="1" min="1" max="${stockActual}">
        <button data-role="add-to-cart" onclick="agregarAlCarrito(${producto.id}, this)" ${stockActual <= 0 ? 'disabled' : ''}>Agregar</button>
        <button class="eliminar" onclick="eliminarProducto(${producto.id})">Eliminar</button>
    </section>
    `;
}

function renderizarProductos(productos, titulo = '') {
    const header = document.getElementById('titulo-categoria');
    const table = document.querySelector("#tarjeta");

    if (header) header.innerHTML = titulo ? `<h3>${titulo}</h3>` : '';
    table.innerHTML = "";

    productos.forEach(p => {
        productosData[p.id] = p;
        if (productosStock[p.id] === undefined) productosStock[p.id] = p.rating.count;
        const fila = document.createElement("div");
        fila.dataset.productId = p.id;
        fila.innerHTML = generarTarjetaProducto(p);
        table.appendChild(fila);
    });
}

function cargarProductos() {
    fetch("https://fakestoreapi.com/products")
        .then((res) => res.json())
        .then((data) => {
            productosData = {};
            renderizarProductos(data);
        })
        .catch((error) => console.error(error));
}

function cargarCat() {
    fetch('https://fakestoreapi.com/products/categories')
        .then((response) => response.json())
        .then((data) => {
            const select = document.querySelector("#categoria");
            data.forEach(categoria => {
                const option = document.createElement("option");
                option.value = categoria;
                option.textContent = categoria;
                select.appendChild(option);
            });
        })
        .catch((error) => console.error(error));
}

function filtrarCat() {
    const categoria = document.querySelector("#categoria").value;
    if (!categoria) {
        cargarProductos();
        return;
    }

    fetch('https://fakestoreapi.com/products/category/' + categoria)
        .then((response) => response.json())
        .then((data) => {
            renderizarProductos(data, `Productos de la categoría ${categoria}`);
        })
        .catch((error) => console.error(error));
}

function ordenar() {
    const ordenar = document.querySelector("#ordenar").value;
    if (!ordenar) {
        cargarProductos();
        return;
    }

    fetch("https://fakestoreapi.com/products?sort=" + ordenar)
        .then((response) => response.json())
        .then((data) => {
            const titulo = ordenar === 'asc' ? 'Productos ordenados por ID ascendente' : 'Productos ordenados por ID descendente';
            renderizarProductos(data, titulo);
        })
        .catch((error) => console.error(error));
}

function eliminarProducto(id) {
    fetch("https://fakestoreapi.com/products/" + id, {
        method: "DELETE"
    })
        .then((response) => response.json())
        .then((data) => {
            alert("Has borrado con éxito el producto: " + data.title);
            const producto = document.querySelector(`[data-product-id="${id}"]`);
            if (producto) producto.remove();
        })
        .catch((error) => console.error(error));
}

function limpiarFiltros() {
    document.querySelector("#categoria").value = "";
    document.querySelector("#ordenar").value = "";
    cargarProductos();
}

function ensureCarritoTable() {
    let table = document.getElementById('carrito');
    if (!table) {
        table = document.createElement('table');
        table.id = 'carrito';
        const hero = document.querySelector('#hero');
        if (hero) {
            hero.insertAdjacentElement('afterend', table);
        } else {
            document.body.prepend(table);
        }
    }
}

window.addEventListener('DOMContentLoaded', () => {
    const y = document.getElementById('year');
    if (y) y.textContent = new Date().getFullYear();
});

function carritoTemplate(total) {
    return `
      <thead>
        <tr>
          <th>Producto</th>
          <th>Precio</th>
          <th>Cantidad</th>
          <th>Total</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
        <tr>
          <td class="carrito-total-label" colspan="3">Total carrito</td>
          <td class="carrito-total-value" colspan="2">${total.toFixed(2)}€</td>
        </tr>
      </tfoot>
    `;
}

function renderCarritoTabla() {
    const table = document.querySelector('#carrito');
    if (cart.length === 0) {
        if (table) table.remove();
        return;
    }

    ensureCarritoTable();
    const tabla = document.querySelector('#carrito');
    tabla.innerHTML = carritoTemplate(calcularTotalCarrito());

    const tbody = tabla.querySelector('tbody');
    cart.forEach(item => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${item.title}</td>
          <td>${item.price}€</td>
          <td>${item.quantity}</td>
          <td>${(item.price * item.quantity).toFixed(2)}€</td>
          <td>
            <label class="cantidad-label" for="remove-${item.id}">Cantidad</label>
            <input id="remove-${item.id}" type="number" class="removeQty" min="1" max="${item.quantity}" value="1">
            <button onclick="eliminarCantidadDelCarrito(${item.id}, this)">Eliminar</button>
          </td>
        `;
        tbody.appendChild(tr);
    });
}

function updateStockDisplay(id) {
    //es para actualizar el stock disponible de un producto en la tienda
    const stock = productosStock[id] ?? 0;
    const stockSpan = document.getElementById(`stock-${id}`);
    if (stockSpan) stockSpan.textContent = stock;

    const card = document.querySelector(`[data-product-id="${id}"]`);
    if (card) {
        const qtyInput = card.querySelector('.cantidad');
        // [data-role="add-to-cart"] es para obtener el boton de agregar al carrito
        const addBtn = card.querySelector('[data-role="add-to-cart"]');
        if (qtyInput) qtyInput.max = Math.max(stock, 0);
        if (addBtn) addBtn.disabled = stock <= 0;
    }
}

function agregarAlCarrito(id, btn) {
    const cantidadInput = btn.parentElement.querySelector('input[type="number"]');
    let cantidadSolicitada = parseInt(cantidadInput?.value) || 1;
    const producto = productosData[id];
    if (!producto) return;

    const disponible = productosStock[id] ?? producto.rating.count;
    const cantidadAAgregar = Math.min(cantidadSolicitada, disponible);

    if (cantidadAAgregar <= 0) {
        alert(`No hay stock disponible de "${producto.title}"`);
        return;
    }

    const existente = cart.find(p => p.id === id);
    if (existente) {
        existente.quantity += cantidadAAgregar;
    } else {
        cart.push({ id: id, title: producto.title, price: producto.price, quantity: cantidadAAgregar });
    }

    productosStock[id] = disponible - cantidadAAgregar;
    updateStockDisplay(id);
    alert(`Agregaste ${cantidadAAgregar} unidad(es) de "${producto.title}"`);
    renderCarritoTabla();
}

function eliminarCantidadDelCarrito(id, btn) {
    //parentElement es para obtener el elemento padre de un elemento
    const input = btn.parentElement.querySelector('.removeQty');
    // para que la cantidad a eliminar sea al menos 1
    const solicitada = Math.max(1, parseInt(input?.value) || 1);
    // para que la cantidad a eliminar no sea mayor que la cantidad en el carrito
    const item = cart.find(p => p.id === id);
    if (!item) return;

    const aEliminar = Math.min(solicitada, item.quantity);
    item.quantity -= aEliminar;
    // para actualizar el stock cuando se elimina una cantidad del carrito
    productosStock[id] = (productosStock[id] ?? (productosData[id]?.rating.count ?? 0)) + aEliminar;
    updateStockDisplay(id);

    if (item.quantity <= 0) {
        cart = cart.filter(p => p.id !== id);
    }
    renderCarritoTabla();
}

function mostrarCarrito() {
    if (cart.length === 0) {
        if (document.querySelector('#carrito')) document.querySelector('#carrito').remove();
        alert('El carrito está vacío');
        return;
    }
    renderCarritoTabla();
    const tabla = document.querySelector('#carrito');
    if (!tabla) return;
    tabla.hidden = !tabla.hidden;
}

function calcularTotalCarrito() {
    //reduce es para reducir un array a un solo valor
    return cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
}