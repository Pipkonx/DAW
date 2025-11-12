// Cuando la página carga
document.addEventListener("DOMContentLoaded", async () => {
    const tabla = document.getElementById("tabla-alumnos");
    const tbody = tabla.querySelector("tbody");
    const cargando = document.getElementById("cargando");

    try {
        // Petición al PHP que devuelve JSON
        const respuesta = await fetch("php/listaralumnos.php");
        const datos = await respuesta.json();

        // Si hay alumnos
        if (datos.length > 0) {
            datos.forEach(alumno => {
                const fila = document.createElement("tr");
                fila.innerHTML = `
                    <td>${alumno.id}</td>
                    <td>${alumno.nombre}</td>
                    <td>${alumno.apellido}</td>
                    <td>${alumno.nota}</td>
                `;
                tbody.appendChild(fila);
            });
            cargando.style.display = "none";
            tabla.style.display = "table";
        } else {
            cargando.textContent = "No hay registros de alumnos.";
        }

    } catch (error) {
        cargando.textContent = "Error al cargar los datos.";
        console.error(error);
    }
});

function cargalista() {
    fetch("php/listaralumnosfacil.php")
        .then(response => response.text()) // Convierte la respuesta a texto
        .then(data => {
            document.getElementById('lista-alumnos').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
}

function subir() {
    const nombre = document.getElementById("nombre").value;
    const apellido = document.getElementById("apellido").value;
    const nota = document.getElementById("nota").value;
    if (!nombre || !apellido || !nota) {
        alert("Completa lso datos")
    }
    fetch("php/altaUsuario.php", {
        method: "POST",
        body: new URLSearchParams({
            nombre,
            apellido,
            nota
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.exito) {
                alert("Alumno dado de alta correctamente");
                // Recargar la tabla o lista después de añadir
                cargalista();
            } else {
                alert("Error al dar de alta: " + (data.mensaje || "Error desconocido"));
            }
        })
        .catch(error => {
            console.error("Error en la petición:", error);
            alert("Error al conectar con el servidor");
        });
}
