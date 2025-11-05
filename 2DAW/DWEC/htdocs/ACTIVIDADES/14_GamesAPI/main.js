// API DOC = https://www.freetogame.com/api-doc
// API = https://www.freetogame.com/api/games


const filtroPlataforma = document.getElementById('filtroPlataforma');

//


function cargarJuegos() {
    fetch('https://api.allorigins.win/get?url=https://www.freetogame.com/api/games')
        .then((response) => response.json())
        .then((datos) => {
            const games = JSON.parse(datos.contents);
            crearCard(games);
        });
}

function crearCard(datos) {
    const contenedor = document.getElementById('contenedor');
    for (let i = 0; i < datos.length; i++) {
        const div = document.createElement('div');
        div.classList.add('card');
        div.innerHTML = `
        <img id="imgJuego" src="${datos[i].thumbnail}" alt="${datos[i].title}">
        <div></dev><h2>${datos[i].title}</h2>
        <p class="genero">${datos[i].genre}</p>
        <p>${datos[i].short_description}</p>
        <a href="${datos[i].game_url}" target="_blank">Jugar</a>
        <p class="plataforma">Plataforma: ${datos[i].platform}</p>
        <p>Publicado por: ${datos[i].publisher}</p>
        <p>Desarrolladores: ${datos[i].developer}</p>
        <p>Fecha lanzamiento: ${datos[i].release_date}</p></div>`;
        contenedor.appendChild(div);
    }
}

//boton de reiniciar
document.getElementById('btnReiniciar').addEventListener('click', () => {
    inputBusqueda.value = '';
    filtroTipo.value = '';
    contenedor.innerHTML = '';
    filtroPlataforma = '';
    cargarJuegos();
});


// input juego
let temporizadorBusqueda;
inputBusqueda.addEventListener('input', () => {
    clearTimeout(temporizadorBusqueda);
    temporizadorBusqueda = setTimeout(() => buscarJuego(), 200);
});
