function mostrarSeleccion() {
    let selectElement = document.getElementById("frutas");
    let seleccionados = [];
    for (let opcion of selectElement.options) {
        if (opcion.selected) {
            seleccionados.push(opcion.value);
        }
    }

    document.getElementById("resultado").textContent = seleccionados.length > 0 ? `Has seleccionado ${seleccionados.join(", ")}` : "No has seleccionado nada";
}