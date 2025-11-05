function buscaArticulo() {
    let codigo = document.getElementById("codigoD").value;
    let codigos = document.getElementsByClassName("codigo");
    encontrado = false;
    for (let i = 0; i < codigos.length; i++) {
        if (codigo == codigos[i].textContent) {
            encontrado = true;
            let  fila = codigos[i].parentNode.children;
            document.getElementById("descripcionD").value = fila[1].textContent;
            document.getElementById("cantidadD").value = fila[2].textContent;
            document.getElementById("precioD").value = fila[3].textContent;
        }
    }
    if (!encontrado) {
        document.getElementById("descripcionD").value = "Articulo no encontrado";
        document.getElementById("cantidadD").value = "";
        document.getElementById("precioD").value = "";
    }
}

