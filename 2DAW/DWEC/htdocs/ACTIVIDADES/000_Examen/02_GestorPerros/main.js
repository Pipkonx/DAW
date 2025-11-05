// aprovechamiento de la actividad por eso mantengo el generar, por falta de tiempo

listaPerros = [];
const contenedor = document.getElementById("imagen");

function generar() {
    const cantidad = parseInt(document.getElementById("numero").value);
    contenedor.innerHTML = "";
    const nombrePerro = document.getElementById("nombrePerro").value;

    for (let i = 0; i < cantidad; i++) {
        fetch('https://dog.ceo/api/breeds/image/random')
            .then(response => response.json())
            .then(data => {
                const img = document.createElement("img");
                img.classList.add("card-img-top");
                listaPerros.push({ nombre: nombrePerro, foto: data.message });
                img.src = data.message;
                contenedor.appendChild(img);
            })
            .catch(error => console.error(error));
    }
}

function mostrarHistorial() {
    const contenedorD = document.getElementById("historial");
    contenedor.innerHTML = "";
    contenedorD.innerHTML = "";

    console.log(listaPerros);
    for (let i = 0; i < listaPerros.length; i++) {
        const wrapper = document.createElement("div");
        wrapper.classList.add("card", "mb-2");

        const img = document.createElement("img");
        img.classList.add("card-img-top");
        img.src = listaPerros[i].foto;

        const cardBody = document.createElement("div");
        cardBody.classList.add("card-body");

        const title = document.createElement("h5");
        title.classList.add("card-title");
        title.textContent = listaPerros[i].nombre;

        cardBody.appendChild(title);
        wrapper.appendChild(img);
        wrapper.appendChild(cardBody);
        contenedorD.appendChild(wrapper);
    }
}