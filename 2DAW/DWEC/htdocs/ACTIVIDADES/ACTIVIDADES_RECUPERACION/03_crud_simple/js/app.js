
let clienteEditandoId = null;
let filaSeleccionada = null;

const clientesContainer = document.querySelector("#clientes-container");
const form = document.querySelector("form");
// los inputs del index
const nombreInput = document.querySelector("#nombre");
const apellidosInput = document.querySelector("#apellidos");
const telefonoInput = document.querySelector("#telefono");
const emailInput = document.querySelector("#email");
const direccionInput = document.querySelector("#direccion");
// como saber cuando tengo que poner innerHtml y value
// cuando quiero acceder al valor de un input uso value
// cuando quiero acceder al texto que hay dentro de un elemento uso innerHTML



async function mostrarClientes() {
    const response = await fetch("./api/clientes.php");
    const data = await response.json();
    // limpiamos para que no se dupliquen
    clientesContainer.innerHTML = "";

    if (data.length > 0) {
        let tableHTML = `
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>apellidos</th>
                        <th>telefono</th>
                        <th>email</th>
                        <th>direccion</th>
                        <th>fecha_alta</th>
                        <th>accion</th>
                    </tr>
                </thead>
                <tbody>
        `;

        data.forEach(cliente => {
            tableHTML += `
                <tr data-id="${cliente.id}">
                    <td>${cliente.id}</td>
                    <td>${cliente.nombre}</td>
                    <td>${cliente.apellidos}</td>
                    <td>${cliente.telefono}</td>
                    <td>${cliente.email}</td>
                    <td>${cliente.direccion}</td>
                    <td>${cliente.fecha_alta}</td>
                    <td><button class="btn-eliminar" data-id="${cliente.id}">Eliminar</button></td>
                </tr>
            `;
        });

        tableHTML += `
                </tbody>
            </table>
        `;
        clientesContainer.innerHTML = tableHTML;

        document.querySelectorAll(".btn-eliminar").forEach(button => {
            button.addEventListener("click", function(e) {
                e.stopPropagation(); // Evita que el evento de la fila se dispare
                const id = this.dataset.id;
                eliminar(id);
            });
        });

        document.querySelectorAll("#clientes-container tbody tr").forEach(row => {
            row.addEventListener("click", function() {
                const id = this.dataset.id;
                cargarClienteEnFormulario(id, this);
            });
        });
    } else {
        clientesContainer.innerHTML = "<p>No hay clientes para mostrar.</p>";
    }
}

document.addEventListener("DOMContentLoaded", function () {
    mostrarClientes();


    // tiene que ser async porque se va a esperar a la respuesta
    form.addEventListener("submit", async function (e) {
        // esto es para prevenir que se recargue la pagina
        e.preventDefault();

        //hacemos las validaciones desde JS
        const campos = [
            { valor: nombreInput.value.trim(), nombre: "nombre" },
            { valor: apellidosInput.value.trim(), nombre: "apellidos" },
            { valor: telefonoInput.value.trim(), nombre: "teléfono" },
            { valor: emailInput.value.trim(), nombre: "email" },
            { valor: direccionInput.value.trim(), nombre: "dirección" },
        ];
        // los value los tengo que poner aqui y no cuando los cojo del input porque sino que me coge el valor anterior que tenia el input


        // para no tener que repetir el if tanto lo hacemos pero en un bucle for
        for (const campo of campos) {
            if (campo.valor === "") {
                // alert(`El ${campo.value} no puede estar vacío.`);
                // alert(`El ${campo.innerHTML} no puede estar vacío.`);
                alert(`El ${campo.nombre} no puede estar vacío.`);
                // es nombre porque es el primer campo que muestra el alert
                return;
            }
        }


        //! VALIDACIONES TELEFONO
        // isNaN es para verificar si es un numero
        if (isNaN(telefonoInput.value)) {
            alert("El teléfono debe contener solo números.");
            return;
        }

        if(telefonoInput.value.length < 9 || telefonoInput.value.length > 13) {
            alert("El teléfono debe tener entre 9 y 13 dígitos.");
            return;
        }

        // para verificar que el email es un email coorecto comprobamos que tenga el @
        if (!emailInput.value.includes("@")) {
            alert("El email no es válido.");
            return;
        }







        //diferencia entre async y await es que async es para indicarle que la funcion va a ser asincrona
        // y await es para esperar a que se resuelva la promesa
        // que la promesa es para esperar a que se resuelva
        let url = "./api/clientes.php";
        let method = "POST";
        let bodyData = {
            nombre: nombreInput.value,
            apellidos: apellidosInput.value,
            telefono: telefonoInput.value,
            email: emailInput.value,
            direccion: direccionInput.value
        };

        if (clienteEditandoId) {
            url = `./api/clientes.php`;
            method = "PUT";
            bodyData.id = clienteEditandoId;
        }

        const response = await fetch(url, {
            method: method,
            headers: {
                // esto es para indicarle que le vamos a pasar un json
                "Content-Type": "application/json"
            },
            // json.stringify es para convertir el objeto en una cadena de texto
            body: JSON.stringify(bodyData)
        });

        // para cuando lo devuelva
        const result = await response.json();

        if (result.success) {
            alert(clienteEditandoId ? "Cliente actualizado correctamente." : "Cliente agregado correctamente.");
            // para dejar limpio de nuevo los campos
            nombreInput.value = "";
            apellidosInput.value = "";
            telefonoInput.value = "";
            emailInput.value = "";
            direccionInput.value = "";
            clienteEditandoId = null; // Resetear el ID de edición
            // actualiamos la lista de clientes para que no tengamos que recargar la pagina
            mostrarClientes();
        }
    });
});



function eliminar($id) {
    fetch("./api/clientes.php", {
        method: "DELETE",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ id: $id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert("Cliente eliminado correctamente.");
            // lo mismo actualizamos aqui para que no tengamos que recargar la pagina
            mostrarClientes();
        } else {
            alert("Error al eliminar cliente: " + data.message);
        }
    });
}

async function cargarClienteEnFormulario(id, fila) {
    const response = await fetch(`./api/clientes.php?id=${id}`);
    const cliente = await response.json();

    if (cliente) {
        nombreInput.value = cliente.nombre;
        apellidosInput.value = cliente.apellidos;
        telefonoInput.value = cliente.telefono;
        emailInput.value = cliente.email;
        direccionInput.value = cliente.direccion;
        clienteEditandoId = cliente.id;

        // le quitamos la clase con el color
        if (filaSeleccionada) {
            filaSeleccionada.classList.remove("fila-seleccionada");
        }
        // le agregamos la clase a la fila con el color
        fila.classList.add("fila-seleccionada");
        filaSeleccionada = fila;
    } else {
        alert("Error al cargar cliente.");
    }
}