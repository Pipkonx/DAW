const selMarca = document.querySelector("#select-marcas");
const selModelo = document.querySelector("#select-modelos");
const contModelo = document.querySelector("#contador-modelos");
const imgMarca = document.querySelector("#logo-marca");
const divInfoModelo = document.querySelector("#info-modelo");
const btnComprar = document.querySelector("#btn-comprar");
const cuerpoTablaCompras = document.querySelector("#tabla-compras tbody");
const inputBuscadorModelo = document.querySelector("#input-buscador-modelo");
const resultadosBusquedaModelo = document.querySelector("#resultados-busqueda-modelo");
const marcaModeloBuscado = document.querySelector("#marca-modelo-buscado");

let modelosDisp = [];
let modSel = null;
let nomMarcaSel = "";



function cargarMarcas() {
    fetch("listarMarcas.php")
        .then((response) => response.json())
        .then((data) => {
            // Si hay marcas
            console.log(data)
            if (data.length > 0) {
                data.forEach(marca => {
                    const option = document.createElement("option");
                    option.value = marca.id;
                    option.textContent = marca.nombre;
                    selMarca.appendChild(option);
                });
            }
        })
        .catch((error) => {
            console.error(error);
        });
}

document.addEventListener("DOMContentLoaded", () => {
    cargarMarcas();
    cargarHistorialCompras();
});

function cargarHistorialCompras() {
    fetch("listarCompras.php")
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cuerpoTablaCompras.innerHTML = ""; // Limpiar tabla antes de añadir
                data.compras.forEach(compra => {
                    const newRow = cuerpoTablaCompras.insertRow();
                    newRow.innerHTML = `<td>${compra.marca}</td><td>${compra.modelo}</td><td>${parseFloat(compra.precio_final).toFixed(2)} €</td>`;
                });
            } else {
                console.error("Error al cargar el historial de compras:", data.message);
            }
        })
        .catch(error => console.error("Error de conexión al cargar el historial de compras:", error));
}

const logos = {
    1: "images/Toyota.png",
    2: "images/Ford.png",
    3: "images/Volkswagen.png",
    4: "images/Renault.png",
    5: "images/Seat.png",
    6: "images/BMW.png",
    7: "images/Audi.png",
    8: "images/Mercedes.png",
    9: "images/Peugeot.png",
    10: "images/Hyundai.png",
}

selMarca.addEventListener("change", () => {
    selModelo.innerHTML = ''; // limpiamos para que no se queden los modelos antiguos
    const idMarcaSel = selMarca.value;
    nomMarcaSel = selMarca.options[selMarca.selectedIndex].textContent;
    if (idMarcaSel) {
        // console.log('Marca seleccionada:', idMarcaSel, 'Logo:', logos[idMarcaSel]);
        imgMarca.src = logos[idMarcaSel];
        fetch(`listarModelos.php?id_marca=${idMarcaSel}`)
            .then((response) => response.json())
            .then((data) => {
                console.log(data)
                // Cambio modelos
                if (data.length > 0) {
                    modelosDisp = data;
                    data.forEach(mod => {
                        const option = document.createElement("option");
                        option.value = mod.id;
                        option.textContent = mod.nombre;
                        selModelo.appendChild(option);
                    });
                }
                contModelo.textContent = `Número de modelos: ${data.length}`;

            })
            .catch((error) => {
                console.error(error);
            });
    }
});

resultadosBusquedaModelo.addEventListener("click", (e) => {
    const elementoClicado = e.target;
    if (elementoClicado.classList.contains("resultado-busqueda-item")) {
        const nomMarca = elementoClicado.dataset.nombreMarca;
        marcaModeloBuscado.textContent = `Marca del modelo: ${nomMarca}`;
    }
});


selModelo.addEventListener("change", () => {
    btnComprar.style.display = "none";
    const idMod = selModelo.value;
    const nomMod = selModelo.options[selModelo.selectedIndex].textContent;

    let modActual = null;
    for (const mod of modelosDisp) {
        if (mod.id == idMod) {
            modActual = mod;
            break;
        }
    }
    modSel = modActual;

    if (modSel) {
            divInfoModelo.innerHTML = "";
            precioBase = parseFloat(modSel.precio);
            calcularPrecioFinal();
            btnComprar.style.display = "block";

        divInfoModelo.innerHTML = `
            <p>Modelo: ${nomMod}</p>
            <label>Precio:</label>
            <input type="number" id="input-precio" value="${modSel.precio ? modSel.precio : ''}" ${modSel.precio ? 'disabled' : ''}>
            ${modSel.precio ? '' : '<button id="guardar-precio">Guardar precio</button>'}
        `;

        if (!modSel.precio) {
            const btnGuardarPrecio = document.querySelector("#guardar-precio");

            btnGuardarPrecio.addEventListener("click", () => {
                const precioNuevo = parseFloat(document.querySelector("#input-precio").value);
                if (isNaN(precioNuevo) || precioNuevo <= 0) {
                    alert("Introduce un precio válido");
                    return;
                }

                fetch('actualizarPrecio.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `id_modelo=${idMod}&precio=${precioNuevo}`
                })
                    .then(res => res.json())
                    .then(resp => {
                        if (resp.success) {
                            divInfoModelo.innerHTML = `<p>Precio guardado correctamente: ${precioNuevo} €</p>`;
                            // Actualizo el modelo guardado con el nuevo precio
                            modSel.precio = precioNuevo;
                            // Vuelvo a pintar el divInfoModelo para que aparezca el precio nuevo y bloqueo el input
                            divInfoModelo.innerHTML = `
                                <p>Modelo: ${nomMod}</p>
                                <label>Precio:</label>
                                <br>
                                <input type="number" id="input-precio" min="0" step="0.01" value="${modSel.precio}" disabled>
                            `;
                        }
                    });
            });
        }
    }
});


let precioBase = 0; // Lo establecerás cuando el usuario elija modelo

// Función para calcular el precio final
function calcularPrecioFinal() {
    const form = document.getElementById('form-extras');
    let extras = 0;

    // Color (radio)
    const colores = form.elements['color'];
    for (let i = 0; i < colores.length; i++) {
        if (colores[i].checked) {
            extras += parseInt(colores[i].value);
        }
    }

    // Puertas (radio)
    const puertas = form.elements['puertas'];
    for (let i = 0; i < puertas.length; i++) {
        if (puertas[i].checked) {
            extras += parseInt(puertas[i].value);
        }
    }

    // Aire acondicionado (checkbox)
    if (form.elements['aire'].checked) {
        extras += parseInt(form.elements['aire'].value);
    }

    // Llantas (checkbox)
    if (form.elements['llantas'].checked) {
        extras += parseInt(form.elements['llantas'].value);
    }

    // Precio final = base + extras
    const precioFinal = precioBase + extras;
    document.getElementById('precioFinal').textContent = precioFinal + ' €';
}

// Escuchar cambios en cualquier checkbox o radio para actualizar precio
document.getElementById('form-extras').addEventListener('change', calcularPrecioFinal);

// Simulación cuando seleccionan modelo (debes conectar esto a tu lógica real)
function alSeleccionarModelo(precio) {
    precioBase = precio;
    calcularPrecioFinal();
}

btnComprar.addEventListener("click", () => {
    const marca = nomMarcaSel;
    const modeloNombre = modSel.nombre;
    const precioFinal = parseFloat(document.getElementById("precioFinal").textContent);

    fetch('guardarCompra.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `marca=${marca}&modelo=${modeloNombre}&precio_final=${precioFinal}`
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.success) {
            const newRow = cuerpoTablaCompras.insertRow();
            newRow.innerHTML = `<td>${marca}</td><td>${modeloNombre}</td><td>${precioFinal} €</td>`;
            alert("Compra realizada con éxito!");
        } else {
            alert("Error al guardar la compra.");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Error de conexión al guardar la compra.");
    });
});

inputBuscadorModelo.addEventListener("input", () => {
    const textoBusqueda = inputBuscadorModelo.value;
    resultadosBusquedaModelo.innerHTML = "";
    marcaModeloBuscado.textContent = "";

    if (textoBusqueda.length > 0) {
        fetch(`buscarModelos.php?query=${textoBusqueda}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    data.modelos.forEach(mod => {
                        const p = document.createElement("p");
                        p.textContent = mod.modelo_nombre;
                        p.dataset.idModelo = mod.id;
                        p.dataset.nombreMarca = mod.marc_nombre;
                        p.dataset.precio = mod.precio;
                        p.classList.add("resultado-busqueda-item");
                        resultadosBusquedaModelo.appendChild(p);
                    });
                } else {
                    console.error("Error al buscar modelos:", data.message);
                }
            })
            .catch(error => console.error("Error de conexión al buscar modelos:", error));
    }
});
