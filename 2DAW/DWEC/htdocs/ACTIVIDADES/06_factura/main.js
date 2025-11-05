function calcularTotal() {
    let subtotal = document.querySelectorAll("#subtotal");
    let totalFactura = 0;
    for (let i = 1; i < subtotal.length; i++) {
        totalFactura += parseFloat(subtotal[i].textContent);
    }
    document.getElementById("total").innerHTML = totalFactura.toFixed(2);
}

function agregarProducto() {
    let codigo = document.querySelector("#codigo").value;
    let descripcion = document.querySelector("#descripcion").value;
    let cantidad = document.querySelector("#cantidad").value;
    let precio = document.querySelector("#precio").value;
    let total = (cantidad * precio).toFixed(2);

    let NuevaFila = [codigo, descripcion, cantidad, precio, total];
    let fila = document.createElement("tr");
    document.getElementById("tabla").appendChild(fila);

    // celdas con los datos
    for (let i = 0; i < NuevaFila.length; i++) {
        let columna = document.createElement("td");
        if (i === 4) {
            columna.id = "subtotal";
        }
        columna.innerHTML = NuevaFila[i];
        fila.appendChild(columna);
    }

    // boton eliminar
    let celdaEliminar = document.createElement("td");
    let boton = document.createElement("button");
    boton.innerHTML = '❌';
    boton.addEventListener("click", function () {
        fila.remove();
        calcularTotal();
    });
    celdaEliminar.appendChild(boton);
    fila.appendChild(celdaEliminar);

    // celda boton modificar
    let celdaModificar = document.createElement("td");
    let botonModificar = document.createElement("button");
    botonModificar.innerHTML = '🔑';
    botonModificar.addEventListener("click", function () {
        // cargar valores
        let celdas = fila.querySelectorAll("td");
        document.getElementById("codigoD").value = celdas[0].textContent;
        document.getElementById("descripcionD").value = celdas[1].textContent;
        document.getElementById("cantidadD").value = celdas[2].textContent;
        document.getElementById("precioD").value = celdas[3].textContent;

        // configurar boton acutaliar
        document.getElementById("modificar").onclick = function () {
            celdas[0].textContent = document.getElementById("codigoD").value;
            celdas[1].textContent = document.getElementById("descripcionD").value;
            celdas[2].textContent = document.getElementById("cantidadD").value;
            celdas[3].textContent = document.getElementById("precioD").value;

            let cantidadMod = parseFloat(document.getElementById("cantidadD").value);
            let precioMod = parseFloat(document.getElementById("precioD").value);
            celdas[4].textContent = (cantidadMod * precioMod).toFixed(2);

            calcularTotal();
        };
    });
    celdaModificar.appendChild(botonModificar);
    fila.appendChild(celdaModificar);
    calcularTotal();
}