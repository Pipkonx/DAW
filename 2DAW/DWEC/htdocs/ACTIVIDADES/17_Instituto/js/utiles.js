var id_global = null;

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
                    <td>${alumno.codigo}</td>
                    <td>${alumno.nombre}</td>
                    <td>${alumno.apellidos}</td>
                    <td>${alumno.nota}</td>
                    <td><button onclick=eliminaalumno(${alumno.codigo});>✖️</button>
                    <button onclick=modificaalumno(${alumno.codigo});>✏️</button>
                    <button onclick=verNotas(${alumno.codigo});>Ver</button></td>
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


function verNotas(id) {
    const url = 'php/verNotas.php?id=' + id;
    fetch(url)
        .then(res => res.text())
        .then(data => {
            alert(data);
            console.log(data);
            document.getElementById('lista-notas').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
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

function modificaalumno(id) {
    const url = 'php/modificaalumno.php?id=' + id;
    id_global = id;
    fetch(url)
        .then(res => res.json())
        .then(data => {
            document.getElementById("nombre").value = data[0].nombre;
            document.getElementById("apellidos").value = data[0].apellidos;
            document.getElementById("nota").value = data[0].nota;
        })
        .catch(err => console.error(err));
}

function modificaalumno2() {
    const nombre = document.getElementById("nombre").value;
    const apellidos = document.getElementById("apellidos").value;
    const nota = document.getElementById("nota").value;
    const url = `php/modificaalumno2.php?id=${id_global}&nombre=${encodeURIComponent(nombre)}&apellidos=${encodeURIComponent(apellidos)}&nota=${encodeURIComponent(nota)}`;
    fetch(url)
        .then(res => res.json())
        .then(data => {
            console.log(data);
            alert(data.message);
        })
        .catch(err => console.error(err));
}
