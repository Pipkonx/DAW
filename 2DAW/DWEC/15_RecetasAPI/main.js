const filtroMealType = document.getElementById('filtroMealType');
const filtroDifficulty = document.getElementById('filtroDifficulty');
const filtroCuisine = document.getElementById('filtroCuisine');
const filtroTiempo = document.getElementById('filtroTiempo');
const inputBusqueda = document.getElementById('inputBusqueda');

function cargarRecetas() {
    fetch('https://dummyjson.com/recipes')
        .then(response => response.json())
        .then(datos => {
            crearCard(datos.recipes);
            cargarFiltros(datos.recipes);
        });
}

function crearCard(datos) {
    const contenedor = document.getElementById('contenedor');
    contenedor.innerHTML = '';  // Limpiar contenido anterior

    // Crear las tarjetas de receta
    for (let i = 0; i < datos.length; i++) {
        const receta = datos[i];
        const div = document.createElement('div');
        div.classList.add('card');
        div.innerHTML = `
        <img class="imgReceta" src="${receta.image}" alt="${receta.name}">
        <div>
            <p>id : ${receta.id}</p>
            <h2>${receta.name}</h2>
            <p class="genero">${receta.difficulty}</p>
            <p>Cocina: ${receta.cuisine}</p>
            <p>Tiempo: ${receta.prepTimeMinutes} min</p>
            <p>Reviews: ${receta.reviewCount}</p>
            <p>⭐ ${receta.rating} / 5</p>
            <p>Calorías: ${receta.caloriesPerServing}</p>
        </div>`;
        contenedor.appendChild(div);
    }
}

// Reestablecer filtros
document.getElementById('btnReiniciar').addEventListener('click', () => {
    inputBusqueda.value = '';
    filtroMealType.value = '';
    filtroDifficulty.value = '';
    filtroCuisine.value = '';
    filtroTiempo.value = '';
    cargarRecetas();  // Recargar todas las recetas
});

// Filtros en búsqueda
let temporizadorBusqueda;
inputBusqueda.addEventListener('input', () => {
    clearTimeout(temporizadorBusqueda);
    temporizadorBusqueda = setTimeout(() => { buscarReceta(); }, 200);
});

filtroMealType.addEventListener('change', () => { buscarReceta(); });
filtroDifficulty.addEventListener('change', () => { buscarReceta(); });
filtroCuisine.addEventListener('change', () => { buscarReceta(); });
filtroTiempo.addEventListener('change', () => { buscarReceta(); });

function buscarReceta() {
    const query = inputBusqueda.value.trim();
    const mealType = filtroMealType.value;
    const difficulty = filtroDifficulty.value;
    const cuisine = filtroCuisine.value;
    const tiempoMax = filtroTiempo.value;

    let url = `https://dummyjson.com/recipes/search?q=${query}`;

    if (difficulty) {
        url += `&difficulty=${difficulty}`;
    }

    fetch(url)
        .then(res => res.json())
        .then(data => {
            let recetasFiltradas = [];

            // Aplicamos los filtros manualmente
            for (let i = 0; i < data.recipes.length; i++) {
                let receta = data.recipes[i];
                let cumpleFiltros = true;

                // Filtro por tipo de comida
                if (mealType) {
                    let tieneElTipo = false;
                    
                    // Verificar si el mealType de la receta contiene el tipo seleccionado
                    if (receta.mealType.constructor === Array) {
                        // Si es array, buscar en el array
                        for (let t = 0; t < receta.mealType.length; t++) {
                            if (receta.mealType[t] === mealType) {
                                tieneElTipo = true;
                                break;
                            }
                        }
                    } else if (typeof receta.mealType === 'string') {
                        // Si es string, verificar si contiene el tipo
                        if (receta.mealType === mealType) {
                            tieneElTipo = true;
                        } else {
                            // Buscar en string separado por comas
                            let partes = receta.mealType.split(',');
                            for (let p = 0; p < partes.length; p++) {
                                let tipo = partes[p];
                                // Quitar espacios
                                while (tipo.charAt(0) === ' ') {
                                    tipo = tipo.substring(1);
                                }
                                while (tipo.charAt(tipo.length - 1) === ' ') {
                                    tipo = tipo.substring(0, tipo.length - 1);
                                }
                                if (tipo === mealType) {
                                    tieneElTipo = true;
                                    break;
                                }
                            }
                        }
                    }
                    
                    if (!tieneElTipo) {
                        cumpleFiltros = false;
                    }
                }

                // Filtro por dificultad
                if (difficulty && receta.difficulty !== difficulty) {
                    cumpleFiltros = false;
                }

                // Filtro por cocina
                if (cuisine && receta.cuisine !== cuisine) {
                    cumpleFiltros = false;
                }

                // Filtro por tiempo de preparación
                if (tiempoMax) {
                    let tiempoTotal = receta.prepTimeMinutes;
                    if (tiempoTotal > parseInt(tiempoMax)) {
                        cumpleFiltros = false;
                    }
                }

                // Si cumple con los filtros, agregar a la lista
                if (cumpleFiltros) {
                    recetasFiltradas[recetasFiltradas.length] = receta;
                }
            }

            crearCard(recetasFiltradas);  // Mostrar las recetas filtradas
        });
}

function cargarFiltros(recetas) {
    // Limpiar los filtros antes de cargar nuevos
    filtroMealType.innerHTML = '<option value="">Tipo de comida</option>';
    filtroDifficulty.innerHTML = '<option value="">Dificultad</option>';
    filtroCuisine.innerHTML = '<option value="">Cocina</option>';

    // Inicializar arrays para las opciones
    let mealTypes = [];
    let difficulties = [];
    let cuisines = [];

    // recoore y obten el
    for (let i = 0; i < recetas.length; i++) {
        const receta = recetas[i];

        // Agregar tipos de comida: separar y deduplicar
        if (receta.mealType) {
            // Normaliza a una lista de tipos sin usar isArray
            let tipos = [];
            if (receta.mealType.constructor === Array) {
                tipos = receta.mealType;
            } else if (typeof receta.mealType === 'string') {
                // Separar por comas sin usar .map
                let partes = receta.mealType.split(',');
                for (let p = 0; p < partes.length; p++) {
                    let tipo = partes[p];
                    // Quitar espacios al inicio y final
                    while (tipo.charAt(0) === ' ') {
                        tipo = tipo.substring(1);
                    }
                    while (tipo.charAt(tipo.length - 1) === ' ') {
                        tipo = tipo.substring(0, tipo.length - 1);
                    }
                    if (tipo.length > 0) {
                        tipos[tipos.length] = tipo;
                    }
                }
            }

            // Añade cada tipo individual si no existe
            for (let k = 0; k < tipos.length; k++) {
                const tipo = tipos[k];
                let encontrado = false;
                for (let j = 0; j < mealTypes.length; j++) {
                    if (mealTypes[j] === tipo) {
                        encontrado = true;
                        break;
                    }
                }
                if (!encontrado) {
                    mealTypes[mealTypes.length] = tipo;
                    const option = document.createElement("option");
                    option.value = tipo;
                    option.textContent = tipo;
                    filtroMealType.appendChild(option);
                }
            }
        }

        // Agregar dificultad solo si no está ya en el array
        if (receta.difficulty) {
            let encontrado = false;
            for (let j = 0; j < difficulties.length; j++) {
                if (difficulties[j] === receta.difficulty) {
                    encontrado = true;
                    break;
                }
            }
            if (!encontrado) {
                difficulties[difficulties.length] = receta.difficulty;
                const option = document.createElement("option");
                option.value = receta.difficulty;
                option.textContent = receta.difficulty;
                filtroDifficulty.appendChild(option);
            }
        }

        // Agregar cocina solo si no está ya en el array
        if (receta.cuisine) {
            let encontrado = false;
            for (let j = 0; j < cuisines.length; j++) {
                if (cuisines[j] === receta.cuisine) {
                    encontrado = true;
                    break;
                }
            }
            if (!encontrado) {
                cuisines[cuisines.length] = receta.cuisine;
                const option = document.createElement("option");
                option.value = receta.cuisine;
                option.textContent = receta.cuisine;
                filtroCuisine.appendChild(option);
            }
        }
    }
}