function muestra() {
    fetch("../public/php/listar_alumnos.php")
        .then((response) => response.json())
        .then((data) => {
            const cont = document.getElementById("alumnos");
            const table = document.createElement("table");

            const thead = document.createElement("thead");
            thead.innerHTML = `<tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Nota</th>
      </tr>
      `;

            const tbody = document.createElement("tbody");

            for (let i = 0; i < data.length; i++) {
                const alumno = data[i];
                const tr = document.createElement("tr");
                tr.innerHTML = `
          <td>${alumno.id || ""}</td>
          <td>${alumno.nombre || ""}</td>
          <td>${alumno.apellido || ""}</td>
          <td>${alumno.nota || ""}</td>
        `;
                tbody.appendChild(tr);
            }

            table.appendChild(thead);
            table.appendChild(tbody);
            cont.innerHTML = "";
            cont.appendChild(table);
        })
}


function cargaLista() {
    fetch("../public/php/listar_alumnos_facil.php")
        .then(response => response.text())
        .then(data => {
            document.getElementById('lista-alumnos').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
}