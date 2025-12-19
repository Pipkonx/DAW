var id_global=null;

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
                    <td>
                    <button onclick=eliminaalumno(${alumno.codigo});>Eliminar</button>
                    <button onclick=modificaalumno(${alumno.codigo});>Modificar</button>
                    <button onclick=vernotas(${alumno.codigo});>Ver notas</button>
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
});

function cargalista(){
    fetch("php/listaralumnosfacil.php")
    .then(response => response.text()) // Convierte la respuesta a texto
    .then(data => {
        document.getElementById('lista-alumnos').innerHTML = data;
  })
  .catch(error => console.error('Error:', error));
}

function altaalumno(){
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

function eliminaalumno(id){
   const url = 'php/borraalumno.php?id='+id;

    fetch(url)
      .then(res => res.json())
      .then(data => {
          console.log(data);
          alert(data.message);
      })
      .catch(err => console.error(err));
}

function modificaalumno(id){
    const url = 'php/modificaalumno.php?id='+id;
    id_global = id;
    fetch(url)
      .then(res => res.json())
      .then(data => {
    document.getElementById("nombre").value=data[0].nombre;
    document.getElementById("apellidos").value=data[0].apellidos;
    document.getElementById("nota").value=data[0].nota;
      })
      .catch(err => console.error(err));
}

function modificaalumno2(){
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

function vernotas(id){
    //alert(id);
    fetch("php/listarnotas.php?id=" + id)
    .then(response => response.json())
    .then(data => {
        const notas = document.getElementById('notas');
        notas.innerHTML = ""; // limpio antes de crear nada
        // Crear tabla
        const tabla = document.createElement("table");
        tabla.border = "1";
        tabla.style.borderCollapse = "collapse";
        // Encabezados
        const thead = document.createElement("thead");
        thead.innerHTML = `
            <tr>
                <th>Materia</th>
                <th>Nota</th>
            </tr>
        `;
        tabla.appendChild(thead);
        // Cuerpo de la tabla
        const tbody = document.createElement("tbody");
        data.forEach(nota => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${nota.materia}</td>
                <td>${nota.nota}</td>
            `;
            tbody.appendChild(tr);
        });
        tabla.appendChild(tbody);
        // Insertar tabla en el div
        notas.appendChild(tabla);
    })
    .catch(error => {
        console.error("Error al obtener las notas:", error);
    });
}