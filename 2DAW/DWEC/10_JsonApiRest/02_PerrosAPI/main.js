function generar() {
    const cantidad = parseInt(document.getElementById("numero").value);
    const contenedor = document.getElementById("perros");
    contenedor.innerHTML = "";

    for (let i = 0; i < cantidad; i++) {
        fetch('https://dog.ceo/api/breeds/image/random')
            .then(response => response.json())
            .then(data => {
                const img = document.createElement("img");
                img.src = data.message;
                contenedor.appendChild(img);
            })
            .catch(error => console.error(error));
    }
}