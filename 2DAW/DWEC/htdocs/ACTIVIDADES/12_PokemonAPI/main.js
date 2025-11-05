const coloresPorTipo = {
    normal: '#A8A77A', fire: '#EE8130', water: '#6390F0', electric: '#F7D02C',
    grass: '#7AC74C', ice: '#96D9D6', fighting: '#C22E28', poison: '#A33EA1',
    ground: '#E2BF65', flying: '#A98FF3', psychic: '#F95587', bug: '#A6B91A',
    rock: '#B6A136', ghost: '#735797', dragon: '#6F35FC', dark: '#705746',
    steel: '#B7B7CE', fairy: '#D685AD'
};

let cantidadPorPagina = 20;
let offset = 0;
let todosLosPokemons = [];
let pokemonsPorTipo = [];
let tipoActual = '';
let offsetTipo = 0;

const contenedor = document.querySelector('#pokemons');
const inputBusqueda = document.getElementById('inputBusqueda');
const filtroTipo = document.getElementById('filtroTipo');
const btnMas = document.getElementById('btnMas');

function crearCard(datos) {
    const tipos = datos.types.map(t => t.type.name);
    const tipoPrincipal = tipos[0];
    const colorFondo = coloresPorTipo[tipoPrincipal] || '#A8A77A';
    const idFormateado = String(datos.id).padStart(3, '0');

    const div = document.createElement('div');
    div.classList.add('card');
    div.style.background = `linear-gradient(145deg, ${colorFondo}cc, #ffffffee)`;
    div.innerHTML = `
    <p class="pokemon-id">#${idFormateado}</p>
    <img src="${datos.sprites.front_default}" alt="${datos.name}">
    <h2>${datos.name}</h2>
    <p><strong>Tipo:</strong> ${tipos.join(', ')}</p>
  `;
    contenedor.appendChild(div);
}

// === Cargar tipos desde API ===
function cargarTipos() {
    fetch('https://pokeapi.co/api/v2/type')
        .then(res => res.json())
        .then(data => {
            data.results.forEach(tipo => {
                const option = document.createElement('option');
                option.value = tipo.name;
                option.textContent = tipo.name.charAt(0).toUpperCase() + tipo.name.slice(1);
                filtroTipo.appendChild(option);
            });
        });
}

// === Cargar Pokémon normales ===
function cargarPokemons() {
    const tipoSeleccionado = filtroTipo.value;
    if (tipoSeleccionado) {
        cargarMasPorTipo();
        return;
    }

    for (let id = offset + 1; id <= offset + cantidadPorPagina; id++) {
        fetch(`https://pokeapi.co/api/v2/pokemon/${id}`)
            .then(res => res.json())
            .then(datos => {
                todosLosPokemons.push(datos);
                crearCard(datos);
            })
            .catch(() => { });
    }
    offset += cantidadPorPagina;
}

// === Cargar Pokémon por tipo ===
function cargarPorTipo(tipo) {
    tipoActual = tipo;
    offsetTipo = 0;
    contenedor.innerHTML = '<p>Cargando Pokémon de tipo ' + tipo + '...</p>';
    fetch(`https://pokeapi.co/api/v2/type/${tipo}`)
        .then(res => res.json())
        .then(datos => {
            pokemonsPorTipo = datos.pokemon.map(p => p.pokemon);
            contenedor.innerHTML = '';
            cargarMasPorTipo();
        });
}

function cargarMasPorTipo() {
    const inicio = offsetTipo;
    const fin = offsetTipo + cantidadPorPagina;
    const subset = pokemonsPorTipo.slice(inicio, fin);

    if (subset.length === 0) {
        contenedor.insertAdjacentHTML('beforeend', '<p>No hay más Pokémon de este tipo.</p>');
        return;
    }

    subset.forEach(p => {
        fetch(p.url)
            .then(res => res.json())
            .then(poke => crearCard(poke));
    });

    offsetTipo += cantidadPorPagina;
}

function mostrarPokemons(lista) {
    contenedor.innerHTML = '';
    lista.forEach(p => crearCard(p));
}

// === Búsqueda dinámica con debounce ===
let temporizadorBusqueda;
inputBusqueda.addEventListener('input', () => {
    clearTimeout(temporizadorBusqueda);
    temporizadorBusqueda = setTimeout(() => buscarPokemon(), 200);
});

function buscarPokemon() {
    const termino = inputBusqueda.value.trim().toLowerCase();
    const tipoSeleccionado = filtroTipo.value;
    contenedor.innerHTML = '';

    if (termino === '' && tipoSeleccionado === '') {
        mostrarPokemons(todosLosPokemons);
        return;
    }

    const filtrados = todosLosPokemons.filter(poke => {
        const coincideNombre = poke.name.startsWith(termino);
        const coincideTipo = tipoSeleccionado
            ? poke.types.some(t => t.type.name === tipoSeleccionado)
            : true;
        return coincideNombre && coincideTipo;
    });

    if (filtrados.length > 0) {
        filtrados.forEach(p => crearCard(p));
        return;
    }

    if (termino !== '') {
        fetch(`https://pokeapi.co/api/v2/pokemon/${termino}`)
            .then(res => {
                if (!res.ok) throw new Error('No encontrado');
                return res.json();
            })
            .then(datos => {
                if (tipoSeleccionado && !datos.types.some(t => t.type.name === tipoSeleccionado)) {
                    contenedor.innerHTML = '<p>No se encontró ningún Pokémon con ese tipo.</p>';
                    return;
                }
                crearCard(datos);
            })
            .catch(() => {
                contenedor.innerHTML = '<p>No se encontró ningún Pokémon.</p>';
            });
    } else {
        contenedor.innerHTML = '<p>No se encontró ningún Pokémon.</p>';
    }
}

filtroTipo.addEventListener('change', () => {
    const tipoSeleccionado = filtroTipo.value;
    contenedor.innerHTML = '';
    inputBusqueda.value = '';
    tipoSeleccionado ? cargarPorTipo(tipoSeleccionado) : mostrarPokemons(todosLosPokemons);
});

document.getElementById('btnReiniciar').addEventListener('click', () => {
    inputBusqueda.value = '';
    filtroTipo.value = '';
    contenedor.innerHTML = '';
    tipoActual = '';
    offset = 0;
    offsetTipo = 0;
    todosLosPokemons = [];
    pokemonsPorTipo = [];
    cargarPokemons();
});

btnMas.addEventListener('click', cargarPokemons);

cargarTipos();
cargarPokemons();
