function muestra() {
    fetch("http://localhost/ACTIVIDADES/17_Instituto/public/php/listar_alumnos.php")
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