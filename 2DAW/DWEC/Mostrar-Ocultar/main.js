function CrearOpcion() {
    let nuevo = document.createElement('li');
    let num = document.querySelectorAll("li").length;
    nuevo.textContent = "Opcion " + num;
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