const btnCargar = document.getElementById('btnCargar');
const inputBuscar = document.getElementById('inputBuscar');
const contenedorSeries = document.getElementById('series');
const selectAnnos = document.getElementById('selectAnno');
// const agregarDesc = document.getElementById('agregarDesc');

let seriesData = [];

btnCargar.addEventListener('click', cargarSeries);
inputBuscar.addEventListener('input', buscarSeries);
selectAnnos.addEventListener('change', filtrarPorAnno);




// agregarDesc.addEventListener('click', agregarDesc);

// function agregarDesc() {
//     fetch('https://api.tvmaze.com/shows')
//         .then(response => response.json())
//         .then(data => {
//             seriesData = data;
//             mostrarSeries(seriesData);
//             poblarAnnos(seriesData);
//         })
//         .catch(error => console.error(error));

// }

//!ACTIVIDAD 2
function cargarSeries() {
    fetch('https://api.tvmaze.com/shows')
        .then(response => response.json())
        .then(data => {
            seriesData = data;
            mostrarSeries(seriesData);
            poblarAnnos(seriesData);
        })
        .catch(error => console.error(error));
}

//!ACTIVIDAD 3
function buscarSeries() {
    const query = inputBuscar.value;
    cargarSeries();
    fetch('https://api.tvmaze.com/search/shows?q=' + query)
        .then(response => response.json())
        .then(data => {
            seriesData = [];
            for (let i = 0; i < data.length; i++) {
                seriesData.push(data[i].show);
            }
            mostrarSeries(seriesData);
            poblarAnnos(seriesData);
        })
        .catch(error => console.error(error));
}

function poblarAnnos(series) {
    selectAnnos.innerHTML = '<option value="">Selecciona anno</option>';
    let AnnosUnicos = [];

    for (let i = 0; i < series.length; i++) {
        if (series[i].premiered) {
            // para controlar que no se repitan
            // el substring para recortar el anno
            // substring https://www.w3schools.com/jsref/jsref_substring.asp
            let Anno = series[i].premiered.substring(0, 4);

            let existe = false;
            for (let i = 0; i < AnnosUnicos.length; i++) {
                if (AnnosUnicos[i] == Anno) {
                    existe = true;
                }
            }

            if (existe == false) {
                //push agregamos al final (js)
                AnnosUnicos.push(Anno);
            }
        }
    }

    for (let i = 0; i < AnnosUnicos.length; i++) {
        const option = document.createElement('option');
        option.value = AnnosUnicos[i];
        option.textContent = AnnosUnicos[i];
        // appendchild al final pero del select (dom)
        selectAnnos.appendChild(option);
    }
}

function filtrarPorAnno() {
    const AnnoSeleccionado = selectAnnos.value;
    if (AnnoSeleccionado == "") {
        mostrarSeries(seriesData);
        return;
    }

    let seriesFiltradas = [];
    for (let i = 0; i < seriesData.length; i++) {
        if (seriesData[i].premiered) {
            // el substring para que recorte el a;o
            let Anno = seriesData[i].premiered.substring(0, 4);
            if (Anno == AnnoSeleccionado) {
                seriesFiltradas.push(seriesData[i]);
            }
        }
    }
    mostrarSeries(seriesFiltradas);
}

function mostrarSeries(series) {
    contenedorSeries.innerHTML = '';
    for (let i = 0; i < series.length; i++) {
        const serie = series[i];
        const card = document.createElement('div');
        // las cards del boostrap
        //https://getbootstrap.com/docs/5.0/components/card/
        card.className = 'card';
        // card.className = 'serie-card';

        let imagen = 'https://via.placeholder.com/210x295';
        if (serie.image) {
            imagen = serie.image.medium;
        }

        card.innerHTML =
            // las clases son del boostrap para que se vean bien Rafa-2001%
            `
            <img class='card-img-top' src="${imagen}">
            <div class="serie-body">
                <h2 class='card-title'>${serie.name}</h2>
                <p class='card-text'>${serie.premiered}</p>
                <input type='number' id='nota' placeholder='ingresar una nota'>
            </div>
        `;

        //! ATIVIDAD 4
        card.addEventListener('click', () => {
            if (!card.querySelector('.genres')) {
                // con el ternario
                const genresText = serie.genres ? serie.genres.join(', ') : 'no esta disponible';

                // if (serie.genres) {
                //     const genresText = serie.genres.join(', ');
                // } else {
                //     return 'no esta disponible';
                // }
                const genresElement = document.createElement('p');
                // para agregar la clase
                genresElement.className = 'genres card-text';
                genresElement.textContent = `Géneros: ${genresText}`;
                card.querySelector('.serie-body').appendChild(genresElement);
            }
        });

        contenedorSeries.appendChild(card);
    };

    // ! ACTIVIDAD 6 (NO) 

    // const nota = document.querySelector('#nota').value;

    // nota.addEventListener('change', async (e) => {
    //     e.preventDefault();

    //     const nombre = inputNombre.value;
    //     const id = inputId.value;

    //     if (id === "") {
    //         await fetch('api.php', {
    //             method: 'POST',
    //             body: JSON.stringify({ nombre: nombre })
    //         })
    //     }
    // });

}
