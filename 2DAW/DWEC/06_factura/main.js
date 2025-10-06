function calcularTotal() {
    let subtotal = document.querySelectorAll("#subtotal");
    let totalFactura = 0;
    for (let i = 1; i < subtotal.length; i++) {
        totalFactura += parseFloat(subtotal[i].textContent);
    }
    document.getElementById("total").innerHTML = totalFactura.toFixed(2);
}

function agregarProducto() {
    let codigo = document.querySelector("#Codigo").value;
    let descripcion = document.querySelector("#Descripcion").value;
    let cantidad = document.querySelector("#Cantidad").value;
    let precio = document.querySelector("#Precio").value;
    let total = (cantidad * precio).toFixed(2);

    let boton = document.createElement("button");
    let botonModificar = document.createElement("button");

    let NuevaFila = [codigo, descripcion, cantidad, precio, total];

    let fila = document.createElement("tr");
    document.getElementById("tabla").appendChild(fila);

    for (let i = 0; i < NuevaFila.length; i++) {
        let columna = document.createElement("td");
        // if (i === 0) {
        //     columna.id = "Codigo" + i;
        // }
        // if (i === 1) {
        //     columna.id = "Descripcion"+ i;
        // }
        // if (i === 2) {
        //     columna.id = "Cantidad" + i;
        // }
        // if (i === 3) {
        //     columna.id = "Precio" + i;
        // }
        if (i === 4) {
            columna.id = "subtotal";
        }

        columna.innerHTML = NuevaFila[i];
        fila.appendChild(columna);


        // boton de eliminar
        boton.innerHTML = '❌';
        boton.addEventListener("click", function () {
            // let i = x.parentNode.parentNode.rowIndex;
            // document.getElementById("tabla").deleteRow(i);
            fila.remove();
        });

        fila.appendChild(boton);

        // boton de modificar
        botonModificar.innerHTML = '🔑';
        botonModificar.addEventListener("click", function () {
            let celdas = fila.querySelectorAll("td");
            let CodigoD = document.querySelectorAll("#CodigoD");
            let DescripcionD = document.querySelectorAll("#DescripcionD");
            let CantidaD = document.querySelectorAll("#CantidadD");
            let PrecioD = document.querySelectorAll("#PrecioD");

            // cogems los valores
            CodigoD[0].value = celdas[0].textContent;
            DescripcionD[0].value = celdas[1].innerHTML;
            CantidaD[0].value = celdas[2].innerHTML;
            PrecioD[0].value = celdas[3].innerHTML;

            NuevaFila[i].innerHTML = celdas[i].innerHTML;
            // console.log(celdas[i]);
        });
        fila.appendChild(botonModificar);
    }

    calcularTotal();
}

function modificarProducto(x) {
    // let fila = x.parentNode.parentNode;
    let valores = x.parentNode.parentNode.children;

    document.getElementById("CodigoD").value = valores[0].innerHTML;
    document.getElementById("DescripcionD").value = valores[1].innerHTML;
    document.getElementById("CantidadD").value = valores[2].innerHTML;
    document.getElementById("PrecioD").value = valores[3].innerHTML;

    document.getElementById("modificar").onclick = function () {
        valores[0].innerHTML = document.getElementById("CodigoD").value;
        valores[1].innerHTML = document.getElementById("DescripcionD").value;
        valores[2].innerHTML = document.getElementById("CantidadD").value;
        valores[3].innerHTML = document.getElementById("PrecioD").value;
        console.log(valores[4].innerHTML);

        let cantidad = parseFloat(document.getElementById("CantidadD").value);
        let precio = parseFloat(document.getElementById("PrecioD").value);
        valores[4].innerHTML = (cantidad * precio).toFixed(2);

        calcularTotal();
    };

}