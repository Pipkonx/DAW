var id_global=null;

// Cuando la página carga
document.addEventListener("DOMContentLoaded", async () => {
    const tabla = document.getElementById("tabla-clientes");
    const tbody = tabla.querySelector("tbody");
    const cargando = document.getElementById("cargando");

    try {
        // Petición al PHP que devuelve JSON
        const respuesta = await fetch("php/listarclientes.php");
        const textoRespuesta = await respuesta.text(); // Leer como texto
        console.log("Respuesta cruda del servidor:", textoRespuesta); // Imprimir en consola
        const datos = JSON.parse(textoRespuesta); // Intentar parsear como JSON

        // Si hay clientes
        if (datos.length > 0) {
            datos.forEach(cliente => {
                const fila = document.createElement("tr");
                fila.innerHTML = `
                    <td>${cliente.id}</td>
                    <td>${cliente.nombre}</td>
                    <td>${cliente.apellidos}</td>
                    <td>${cliente.telefono}</td>
                    <td>${cliente.email}</td>
                    <td>${cliente.direccion}</td>
                    <td>
                    <button onclick=eliminacliente(${cliente.id});>Eliminar</button>
                    <button onclick=modificacliente(${cliente.id});>Modificar</button>
                    <button onclick=vermascotas(${cliente.id});>Ver mascotas</button>
                    </td>
                `;
                tbody.appendChild(fila);
            });
            cargando.style.display = "none";
            tabla.style.display = "table";
        } else {
            cargando.textContent = "No hay registros de clientes.";
        }

    } catch (error) {
        cargando.textContent = "Error al cargar los datos.";
        console.error(error);
    }
});

function cargalista(){
    fetch("php/listarclientesfacil.php")
    .then(response => response.text()) // Convierte la respuesta a texto
    .then(data => {
        document.getElementById('lista-clientes').innerHTML = data;
  })
  .catch(error => console.error('Error:', error));
}

function altacliente(){
    const nombre = document.getElementById("nombre").value;
    const apellidos = document.getElementById("apellidos").value;
    const telefono = document.getElementById("telefono").value;
    const email = document.getElementById("email").value;
    const direccion = document.getElementById("direccion").value;

    const url = `php/altacliente.php?nombre=${encodeURIComponent(nombre)}&apellidos=${encodeURIComponent(apellidos)}&telefono=${encodeURIComponent(telefono)}&email=${encodeURIComponent(email)}&direccion=${encodeURIComponent(direccion)}`;

    fetch(url)
      .then(res => res.json())
      .then(data => {
          console.log(data);
          alert(data.message);
      })
      .catch(err => console.error(err));
}

function eliminacliente(id){
   const url = 'php/borracliente.php?id='+id;

    fetch(url)
      .then(res => res.json())
      .then(data => {
          console.log(data);
          alert(data.message);
      })
      .catch(err => console.error(err));
}

function modificacliente(id){
    const url = 'php/modificacliente.php?id='+id;
    id_global = id;
    fetch(url)
      .then(res => res.json())
      .then(data => {
    document.getElementById("nombre").value=data[0].nombre;
    document.getElementById("apellidos").value=data[0].apellidos;
    document.getElementById("telefono").value=data[0].telefono;
    document.getElementById("email").value=data[0].email;
    document.getElementById("direccion").value=data[0].direccion;
      })
      .catch(err => console.error(err));
}

function modificacliente2(){
    const nombre = document.getElementById("nombre").value;
    const apellidos = document.getElementById("apellidos").value;
    const telefono = document.getElementById("telefono").value;
    const email = document.getElementById("email").value;
    const direccion = document.getElementById("direccion").value;
    const url = `php/modificacliente2.php?id=${id_global}&nombre=${encodeURIComponent(nombre)}&apellidos=${encodeURIComponent(apellidos)}&telefono=${encodeURIComponent(telefono)}&email=${encodeURIComponent(email)}&direccion=${encodeURIComponent(direccion)}`;
    fetch(url)
      .then(res => res.json())
      .then(data => {
          console.log(data);
          alert(data.message);
      })
      .catch(err => console.error(err));
}

function vermascotas(id){
    fetch("php/listarmascotas.php?id=" + id)
    .then(response => response.json())
    .then(data => {
        const mascotasDiv = document.getElementById('mascotas');
        mascotasDiv.innerHTML = ""; // limpio antes de crear nada
        // Crear tabla
        const tabla = document.createElement("table");
        tabla.border = "1";
        tabla.style.borderCollapse = "collapse";
        // Encabezados
        const thead = document.createElement("thead");
        thead.innerHTML = `
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Especie</th>
                <th>Raza</th>
                <th>Fecha Nacimiento</th>
            </tr>
        `;
        tabla.appendChild(thead);
        // Cuerpo de la tabla
        const tbody = document.createElement("tbody");
        data.forEach(mascota => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${mascota.id}</td>
                <td>${mascota.nombre}</td>
                <td>${mascota.especie}</td>
                <td>${mascota.raza}</td>
                <td>${mascota.fech-nacimiento}</td>
            `;
            tbody.appendChild(tr);
        });
        tabla.appendChild(tbody);
        // Insertar tabla en el div
        mascotasDiv.appendChild(tabla);
    })
    .catch(error => {
        console.error("Error al obtener las mascotas:", error);
    });
}

function altamascota(){
    const nombre_mascota = document.getElementById("nombre_mascota").value;
    const especie_mascota = document.getElementById("especie_mascota").value;
    const raza_mascota = document.getElementById("raza_mascota").value;
    const fech_nacimiento_mascota = document.getElementById("fech-nacimiento_mascota").value;

    if (id_global === null) {
        alert("Por favor, selecciona un cliente primero.");
        return;
    }

    const url = `php/altamascota.php?id_cliente=${id_global}&nombre=${encodeURIComponent(nombre_mascota)}&especie=${encodeURIComponent(especie_mascota)}&raza=${encodeURIComponent(raza_mascota)}&fech-nacimiento=${encodeURIComponent(fech_nacimiento_mascota)}`;

    fetch(url)
      .then(res => res.json())
      .then(data => {
          console.log(data);
          alert(data.message);
          vermascotas(id_global); // Recargar la lista de mascotas
      })
      .catch(err => console.error(err));
}