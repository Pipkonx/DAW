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
                    <td><button onclick=eliminaalumno(${alumno.id});>✖️</button></td>
                    <td><button onclick=modificaAlumno(${alumno.id});>✏️</button></td>
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

function modificaAlumno(id) {
    const url = `php/modificaAlumno.php?id=${id}`;
    fetch(url)
        .then(res => res.json())
        .then(data => {
            document.querySelector("input#nombre").value = data[0].nombre;
            document.querySelector("input#apellidos").value = data[0].apellido;
            document.querySelector("input#nota").value = data[0].nota;

            console.log(data);
            // alert(data.message);
        })
        .catch(err => console.error(err));
}

function modificar(id) {
    const nombre = document.getElementById("nombre").value;
    const apellidos = document.getElementById("apellidos").value;
    const nota = document.getElementById("nota").value;

    const url = `php/modificaAlumno.php?id=${id}&nombre=${nombre}&apellidos=${apellidos}&nota=${nota}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            console.log(data);
            alert(data.message);
        })
        .catch(err => console.error(err));
}


function cargalista() {
    fetch("php/listaralumnosfacil.php")
        .then(response => response.text()) // Convierte la respuesta a texto
        .then(data => {
            document.getElementById('lista-alumnos').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
}

function altaalumno() {
    const nombre = document.getElementById("nombre").value;
    const apellidos = document.getElementById("apellidos").value;
    const nota = document.getElementById("nota").value;

    const url = `php/altaalumno.php?nombre=${encodeURIComponent(nombre)}&apellidos=${encodeURIComponent(apellidos)}&nota=${encodeURIComponent(nota)}`;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            console.log(data);
            alert(data.message);
        })
        .catch(err => console.error(err));
}

function eliminaalumno(id) {
    const url = 'php/borraalumno.php?id=' + id;

    fetch(url)
        .then(res => res.json())
        .then(data => {
            console.log(data);
            alert(data.message);
        })
        .catch(err => console.error(err));
}