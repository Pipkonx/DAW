// Cuando la página carga
document.addEventListener("DOMContentLoaded", async () => {
    const tabla = document.getElementById("tabla-alumnos");
    const tbody = tabla.querySelector("tbody");
    const cargando = document.getElementById("cargando");

    try {
        const respuesta = await fetch("php/listaralumnos.php");
        const datos = await respuesta.json();

        if (datos.length > 0) {
            // Llenar la tabla
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

            // Llenar la lista <ul>
            const lista = document.getElementById('lista-alumnos');
            lista.innerHTML = "";
            datos.forEach(alumno => {
                const li = document.createElement('li');
                li.textContent = `${alumno.nombre} ${alumno.apellido} - Nota: ${alumno.nota}`;
                lista.appendChild(li);
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
    fetch("php/listaralumnos.php")
        .then(response => response.json())
        .then(datos => {
            const lista = document.getElementById('lista-alumnos');
            lista.innerHTML = "";
            if (datos.length > 0) {
                datos.forEach(alumno => {
                    const li = document.createElement('li');
                    li.textContent = `${alumno.nombre}`;
                    /*${alumno.apellido} - Nota: ${alumno.nota}*/
                    lista.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.textContent = "No hay alumnos";
                lista.appendChild(li);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('lista-alumnos').innerHTML = "<li>Error al cargar los alumnos</li>";
        });
}

function subirAlumno(method = "POST") {
    const nombre = document.getElementById("nombre").value;
    const apellido = document.getElementById("apellido").value;
    const nota = document.getElementById("nota").value;

    if (!nombre || !apellido || !nota) {
        alert("Completa los datos");
        return;
    }

    let url = "php/altaUsuario.php";
    let options = {};

    if (method.toUpperCase() === "POST") {
        options = {
            method: "POST",
            body: new URLSearchParams({ nombre, apellido, nota })
        };
    } else { // GET
        url += `?nombre=${nombre}&apellido=${apellido}&nota=${nota}`;
        options = { method: "GET" };
    }

    fetch(url, options)
        .then(res => res.json())
        .then(data => {
            if (data.exito) {
                alert("Alumno dado de alta correctamente");
                cargalista(); // recarga la lista <ul>
                location.reload(); // recarga la tabla
            } else {
                alert("Error al dar de alta: " + (data.mensaje || "Error desconocido"));
            }
        })
        .catch(err => {
            console.error("Error en la petición:", err);
            alert("Error al conectar con el servidor");
        });
}