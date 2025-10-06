function agregar() {
    let codigo = document.getElementById("codigo").value;
    let descripcion = document.getElementById("descripcion").value;
    let cantidad = document.getElementById("cantidad").value;
    let precio = document.getElementById("precio").value;
    let fila = document.createElement("tr");
    document.getElementById("tabla").appendChild(fila);

    let celda = document.createElement("td");
    celda.textContent = codigo;
    fila.appendChild(celda);

    let celda2 = document.createElement("td");
    celda2.textContent = descripcion;
    fila.appendChild(celda2);

    let celda3 = document.createElement("td");
    celda3.textContent = cantidad;
    fila.appendChild(celda3);

    let celda4 = document.createElement("td");
    celda4.textContent = precio;
    fila.appendChild(celda4);

    let celda5 = document.createElement("td");
    celda5.setAttribute("class", "subtotal");
    celda5.textContent = (cantidad * precio).toFixed(2);
    fila.appendChild(celda5);

    let celda6 = document.createElement("td");
    celda6.innerHTML = '<button onclick="borrar(this)">Borrar</button>';
    celda6.innerHTML += '<button onclick="editar(this)">Editar</button>';
    fila.appendChild(celda6);

    calcularTotal();
}

function calcularTotal() {
    let total = 0;
    let subtotales = document.getElementsByClassName("subtotal");
    for (i = 0; i < subtotales.length; i++) {
        total += parseFloat(subtotales[i].textContent);
    }
    document.getElementById("total").textContent = total.toFixed(2);
}

function borrar(x) {
    var i = x.parentNode.parentNode.rowIndex;
    document.getElementById("tabla").deleteRow(i);
    calcularTotal();
}

function editar(x) {
    let fila = x.parentNode.parentNode;
    let valores = x.parentNode.parentNode.children;
    document.getElementById('codigo2').value = valores[0].innerHTML;
    document.getElementById('descripcion2').value = valores[1].innerHTML;
    document.getElementById('cantidad2').value = valores[2].innerHTML;
    document.getElementById('precio2').value = valores[3].innerHTML;

    document.getElementById('botonmodifica').onclick = function () {
        valores[0].innerHTML = document.getElementById("codigo2").value;
        valores[1].innerHTML = document.getElementById("descripcion2").value
        valores[2].innerHTML = document.getElementById("cantidad2").value
        valores[3].innerHTML = document.getElementById("precio2").value

        let cantidad = parseFloat(document.getElementById('cantidad2').value)
        let precio = parseFloat(document.getElementById('precio2').value)
        valores[4].innerHTML = (cantidad * precio).toFixed(2);

        calcularTotal();
    }
}

