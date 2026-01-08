const filtroTipo = document.querySelector("#tipo");
const listaPokemon = document.querySelector("#lista-pokemon");
const form = document.querySelector("#form-pokemon");
const inputPokemon = document.querySelector("#pokemon");

let todosLosPokemon = [];

function cargarTipos() {
    fetch("https://pokeapi.co/api/v2/type")
        .then(res => res.json())
        .then(data => {
            data.results.forEach(tipo => {
                const option = document.createElement('option');
                option.value = tipo.name;
                // charAt es para convertir la primera en mayuscula
                option.textContent = tipo.name.charAt(0).toUpperCase() + tipo.name.slice(1);
                filtroTipo.appendChild(option);
            });
        });
}


fetch("https://pokeapi.co/api/v2/pokemon")
    .then(res => res.json())
    .then(data => {
        todosLosPokemon = data.results;
    });

form.addEventListener("submit", (e) => {
    const nombre = inputPokemon.value.trim().toLowerCase();

    fetch(`https://pokeapi.co/api/v2/pokemon/${nombre}`)
        .then(res => res.json())
        .then(data => {
            console.log(data);
        })
        .catch(err => console.error("Error al obtener el Pokémon:", err));
});

cargarTipos();