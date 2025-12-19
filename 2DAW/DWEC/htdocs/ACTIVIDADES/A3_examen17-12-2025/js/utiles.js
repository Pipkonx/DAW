var id_global = null;

// Cuando la página carga
document.addEventListener("DOMContentLoaded", async () => {
    // const body = document.querySelector("select-marcas");
    const select = document.querySelector("select-marcas");

    try {
        const respuesta = await fetch("php/listarmarcas.php");
        const datos = await respuesta.json();
        console.log(datos)

        // Si hay marcas
        if (datos.length > 0) {
            datos.forEach(marca => {
                const option = document.querySelector("select");
                option.innerHTML = `<option value="${marca.id}">${marca.nombre}</option>`;
            });
            select.appendChild(option);
        }

    } catch (error) {
    }
});

function cargamodelos() {
    // fetch("php/listaralumnosfacil.php")
    fetch("php/listarmodelos.php")
        .then(response => response.text()) // Convertimos la respuesta a texto
        .then(data => {
            document.getElementById('lista-modelos').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
}


document.addEventListener("DOMContentLoaded", async () => {
    const body = document.querySelector("select-modelos");

    try {
        const respuesta = await fetch("php/listarmodelos.php");
        const datos = await respuesta.json();
        console.log(datos)

        // Si hay modelos
        if (datos.length > 0) {
            datos.forEach(modelo => {
                const option = document.querySelector("select");
                option.innerHTML = `<option value="${modelo.id}">${modelo.nombre}</option>`;
                body.appendChild(option);
            });
        }

    } catch (error) {
    }
});


// prueba 2
// function cargarCategoria() {
//     const categoria = document.querySelector("#categoria");
//     try {
//         const respuesta = fetch("php/listarmodelos.php");
//         const datos = respuesta.json();

//         // Si hay modelos
//         if (datos.length > 0) {
//             datos.forEach(modelo => {
//                 const option = document.createElement("option");
//                 option.value = modelo.id;
//                 option.textContent = modelo.nombre;
//                 categoria.appendChild(option);
//             });
//         }
//     } catch (error) {
//         console.error(error);
//     }
// }

function cargarCategoria() {
    // fetch('http://localhost/ACTIVIDADES/A3_DOCARMO/php/listarmarcas.php')
    fetch('../php/listarmarcas.php')
        .then((response) => response.json())
        .then((data) => {
            const select = document.querySelector("#categoria");
            // data.forEach(categoria => {
            //     const option = document.createElement("option");
            //     option.value = categoria.id;
            //     option.textContent = categoria.nombre;
            //     select.appendChild(option);
            // });

            for (let i = 0; i < data.length; i++) {
                const option = document.createElement("option");
                option.innerHTML = `
                <option value="${data[i].id}">${data[i].nombre}</option>
                `;
                // option.value = data[i].id;
                // option.textContent = data[i].nombre;
                select.appendChild(option);

            }
        })
        .catch((error) => console.error(error));
}

function cargarSubcategorias() {
    const categoria = document.getElementById("categoria").value;
    const subcategoriaSelect = document.getElementById("subcategoria");

    // Limpiar las marcas
    subcategoriaSelect.innerHTML = '<option value="">Modelo</option > ';

    // Si se seleccionó una categoría válida, cargar las subcategorías correspondientes
    if (categoria) {
        const subcats = subcategorias[categoria];
        subcats.forEach(subcat => {
            const opcion = document.createElement("option");
            opcion.value = subcat.toLowerCase();
            opcion.textContent = subcat;
            subcategoriaSelect.appendChild(opcion);
        });
    }
}