function CrearOpcion() {
    let nuevo = document.createElement('li');
    let num = document.querySelectorAll("li").length;
    nuevo.innerHTML = "Opcion " + num + "<button onclick='Borrar(" + num + ");'>Borrar</button>";
    let padre = document.querySelector("#opciones");
    padre.appendChild(nuevo);
}

function EliminarOpcion() {
    // let opcion = document.querySelectorAll("li:last-child");
    //  opcion[0].remove()

    let opcion = document.querySelectorAll("li");
    if (opcion.length > 0) {
        opcion[opcion.length - 1].remove();
    }
}

function Borrar(fila) {
    let opcion = document.querySelectorAll("li");
    opcion[fila].remove();
}

function CrearTabla() {
    let filas = prompt("Numero de filas");
    let columnas = prompt("Numero de columnas");

    let tabla = document.getElementById("e2");

    for (let i = 0; i < filas; i++) {
        let fila = document.createElement('tr');
        for (let j = 0; j < columnas; j++) {
            let celdas = document.createElement('td');
            celdas.innerHTML = "A";
            fila.appendChild(celdas);
        }
        tabla.appendChild(fila);
    }
    document.body.appendChild(tabla)
}

function CambiarColor(celda) {
    celda.innerHTML = parseInt(celda.innerHTML) + 1;
    if (celda.innerHTML >= 10) {
        celda.style.backgroundColor = "lightblue";
    }
}
