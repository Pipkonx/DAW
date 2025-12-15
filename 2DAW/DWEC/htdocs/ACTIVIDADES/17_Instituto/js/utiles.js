var id_global = null;

async function cargarAlumnosPrincipal() {
    const tabla = document.getElementById("tabla-alumnos");
    const tbody = tabla.querySelector("tbody");
    const cargando = document.getElementById("cargando");

    // Limpiar la tabla antes de cargar nuevos datos
    tbody.innerHTML = "";
    cargando.style.display = "block";
    tabla.style.display = "none";

    try {
        const respuesta = await fetch("php/listaralumnos.php");
        const datos = await respuesta.json();

        if (datos.length > 0) {
            datos.forEach(alumno => {
                const fila = document.createElement("tr");
                fila.innerHTML = `
                    <td>${alumno.codigo}</td>
                    <td>${alumno.nombre}</td>
                    <td>${alumno.apellidos}</td>
                    <td>${alumno.nota_media !== null ? parseFloat(alumno.nota_media).toFixed(2) : 'N/A'}</td>
                    <td>
                        <a href="#" onclick="eliminaalumno(${alumno.codigo}); return false;">✖️</a>
                        <a href="#" onclick="modificaalumno(${alumno.codigo}); return false;"">✏️</a>
                        <a href="#" onclick="verNotas(${alumno.codigo}); return false;"">👁️</a>
                    </td>
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
}

// Cuando la página carga
document.addEventListener("DOMContentLoaded", async () => {
    cargarAlumnosPrincipal();
});

function actualizarNota() {
    const id = document.getElementById("id-alumno").value;
    const asignatura = document.getElementById("asignatura").value;
    const nota = document.getElementById("nota-alumno").value;

    const url = `php/actualizarNota.php?id=${id}&asignatura=${asignatura}&nota=${nota}`;

    fetch(url)
        .then(response => response.text())
        .then(data => {
            alert(data);
            document.getElementById("id-alumno").value = "";
            document.getElementById("asignatura").value = "";
            document.getElementById("nota-alumno").value = "";
            verNotas(id);
            cargarAlumnosPrincipal(); // Refrescar la tabla principal
        })
        .catch(error => console.error('Error:', error));
}

function cargarDatosNotaParaEdicion(idAlumno, asignatura, nota) {
    document.getElementById("id-alumno").value = idAlumno;
    document.getElementById("asignatura").value = asignatura;
    document.getElementById("nota-alumno").value = nota;
}


function verNotas(id) {
    document.getElementById("id-alumno").value = id;
    document.getElementById("asignatura").value = "";
    document.getElementById("nota-alumno").value = "";

    // Fetch student name
    fetch("php/modificaalumno.php?id=" + id)
        .then(response => response.json())
        .then(data => {
            if (data.length > 0) {
                document.getElementById('nombre-alumno-editando').textContent = "Editando notas de: " + `${data[0].nombre} ${data[0].apellidos}`;
            } else {
                document.getElementById('nombre-alumno-editando').textContent = 'Editando notas de: Alumno no encontrado';
            }
        })
        .catch(error => {
            console.error("Error al obtener el nombre del alumno:", error);
            document.getElementById('nombre-alumno-editando').textContent = 'Error al cargar el nombre';
        });

    fetch("php/verNotas.php?id=" + id)
        .then(response => response.json())
        .then(data => {
            const notas = document.getElementById('lista-notas');
            notas.innerHTML = "";
            const tabla = document.createElement("table");

            // Encabezados de la tabla
            const thead = document.createElement("thead");
            thead.innerHTML = `
            <tr>
                <th>Materia</th>
                <th>Nota</th>
                <th>Acciones</th>
            </tr>
        `;
            tabla.appendChild(thead);
            const tbody = document.createElement("tbody");
            data.forEach(nota => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                <td>${nota.Asignatura}</td>
                <td>${nota.nota}</td>
                <td><a href="#" onclick="cargarDatosNotaParaEdicion(${nota.codigoAlumno}, '${nota.Asignatura}', ${nota.nota}); return false;">✏️</a><a href="#" onclick="eliminarNota(${nota.codigoAlumno}, '${nota.Asignatura}'); return false;">✖️</a></td>
            `;
                tbody.appendChild(tr);
            });
            tabla.appendChild(tbody);
            notas.appendChild(tabla);
        })
        .catch(error => {
            console.error("Error al obtener las notas:", error);
            alert(`Error al obtener las notas del alumno con ID: ${id} \n\nError: ${error}`);
        });
}

function eliminarNota(idAlumno, asignatura) {
    if (confirm(`¿Estás seguro de que quieres eliminar la nota de ${asignatura}?`)) {
        const url = `php/borrarNota.php?id=${idAlumno}&asignatura=${asignatura}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                verNotas(idAlumno);
                cargarAlumnosPrincipal();
            })
            .catch(error => console.error('Error:', error));
    }
}

function agregarNuevaNota() {
    const id = document.getElementById("id-alumno").value;
    const asignatura = document.getElementById("asignatura").value;
    const nota = document.getElementById("nota-alumno").value;

    if (!id || !asignatura || !nota) {
        alert("Por favor, complete todos los campos para agregar una nueva nota.");
        return;
    }

    const url = `php/agregarNota.php?id=${id}&asignatura=${asignatura}&nota=${nota}`;

    fetch(url)
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            document.getElementById("asignatura").value = "";
            document.getElementById("nota-alumno").value = "";
            verNotas(id);
            cargarAlumnosPrincipal();
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

    const url = `php/altaalumno.php?nombre=${nombre}&apellidos=${apellidos}&nota=${nota}`;

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
    const url = `php/modificaalumno2.php?id=${id_global}&nombre=${nombre}&apellidos=${apellidos}&nota=${nota}`;
    fetch(url)
        .then(res => res.json())
        .then(data => {
            console.log(data);
            alert(data.message);
        })
        .catch(err => console.error(err));
}

