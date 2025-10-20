function cargaArticulos() {
    fetch('https://jsonplaceholder.typicode.com/posts')
        .then((response) => response.json())
        .then((json) => console.log(json));
}

function mostrarArticulos() {
    fetch('https://jsonplaceholder.typicode.com/posts')
        .then((response) => response.json())
        .then((data) => {
            const table = document.querySelector("tbody");
            const contenidoTabla = data;
            for (let i = 0; i < contenidoTabla.length; i++) {
                const fila = document.createElement("tr");
                fila.innerHTML = `
                <td>${contenidoTabla[i].id}</td>
                <td>${contenidoTabla[i].title}</td>
                <td>${contenidoTabla[i].body}</td>
                `;
                table.appendChild(fila);
            }
        })
        .catch((error) => console.log(error));
}

function muestra() {
    const id = document.querySelector("#texto").value;
    // fetch(`https://jsonplaceholder.typicode.com/posts/${id}`)
    fetch(`https://jsonplaceholder.typicode.com/posts/` + id)
        .then((response) => response.json())
        .then((data) => {
            const table = document.querySelector("tbody");
            const fila = document.createElement("tr");
            fila.innerHTML = `
            <td>${data.id}</td>
            <td>${data.title}</td>
            <td>${data.body}</td>
            `;
            table.appendChild(fila);
            // table.innerHTML = fila.innerHTML;
        })
        .catch((error) => console.log(error));
}

function cargarAutor() {
    fetch('https://jsonplaceholder.typicode.com/users')
        .then((response) => response.json())
        .then((data) => {
            const select = document.querySelector("#autor");
            for (let i = 0; i < data.length; i++) {
                const option = document.createElement("option");
                // option.innerHTML = `
                // <option value="${data[i].id}">${data[i].name}</option>
                // `;
                option.value = data[i].id;
                option.textContent = data[i].name;
                select.appendChild(option);

            }
        })
        .catch((error) => console.error(error));
}

function mostrarArticulosHasta() {
    const id = parseInt(document.querySelector("#hasta").value);
    fetch('https://jsonplaceholder.typicode.com/posts')
        .then((response) => response.json())
        .then((data) => {
            const table = document.querySelector("tbody");
            //slice te permite sacar por pantalla la cantidadd que quieras
            const contenidoTabla = data.slice(0, id);
            for (let i = 0; i < contenidoTabla.length; i++) {
                const fila = document.createElement("tr");
                fila.innerHTML = `
                <td>${contenidoTabla[i].id}</td>
                <td>${contenidoTabla[i].title}</td>
                <td>${contenidoTabla[i].body}</td>
                `;
                table.appendChild(fila);
            }
        })
        .catch((error) => console.log(error));
}

function limpiarDesdeFilaUno() {
    const table = document.getElementById("tbody");
    for (let i = 1; i < table.length; i++) {
        table.innerHTML = "";
        i--;
    }
}

function cargarArticulos() {
    const autor = parseInt(document.querySelector("#autor").value);
    fetch('https://jsonplaceholder.typicode.com/posts')
        .then((response) => response.json())
        .then((data) => {
            const table = document.querySelector("tbody");
            table.innerHTML = "";
            document.querySelector("thead").innerHTML = "<tr><th>ID</th><th>Titulo</th><th>Cuerpo</th></tr>";
            for (let i = 0; i < data.length; i++) {
                if (autor == parseInt(data[i].userId)) {
                    const fila = document.createElement("tr");
                    fila.innerHTML = `
                        <td>${data[i].id}</td>
                        <td>${data[i].title}</td>
                        <td>${data[i].body}</td>
                        `;
                    table.appendChild(fila);
                }
            }
        })
        .catch((error) => console.error(error));
}

