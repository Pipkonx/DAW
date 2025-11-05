function sumar() {
    let num1 = parseInt(document.querySelector("#numero1").value);
    let num2 = parseInt(document.querySelector("#numero2").value);

    // if (document.querySelector("#numero1").length == null || document.querySelector("#numero2").length == null) {
    //     alert("Debes introducer un valor");
    // }

    // if (isNaN(num1) || isNan(num2)) {
    //         alert("Debes introducir dos valores");
    // }


    if (document.querySelector("#numero1").value == "" || document.querySelector("#numero2").value == "") {
        alert("Debes introducir dos valores");
    } else {
        let suma = num1 + num2;
        document.querySelector("#resultado").innerHTML = "La suma es : " + suma;
    }
}



var votos = [0, 0, 0];
var totalVotos = 0;

function votar() {
    let votacion = document.getElementsByName("votacion");
    for (let i = 0; i < votacion.length; i++) {
        if (votacion[i].checked) {
            // alert("Has votado " + votacion[i].value);
            votos[i]++;
            totalVotos++;
            // alert("Has votado " + votos[i]);
            // alert("Votos de si: " + votos[0] + "  votos de no: " + votos[1] + "  votos de s/n: " + votos[2]);
        }
        // let porsi = (votos[0] / totalVotos * 100) + "%";
        // let porno = (votos[1] / totalVotos * 100) + "%";
        // let porsino = (votos[2] / totalVotos * 100) + "%";
        let porsi = (votos[0] / totalVotos * 100).toFixed(2);
        let porno = (votos[1] / totalVotos * 100).toFixed(2);
        let porsino = (votos[2] / totalVotos * 100).toFixed(2);

        document.getElementById("votoSi").innerHTML = votos[0] + " / " + totalVotos + " " + porsi + "%";
        // document.getElementById("votoSi").innerHTML = votos[0] + " / " + totalVotos + " " + (votos[0] / totalVotos * 100).toFixed(2) + "%";
        document.getElementById("votoNo").innerHTML = votos[1] + " / " + totalVotos + "  " + porno + "%";
        document.getElementById("votoSiNo").innerHTML = votos[2] + " / " + totalVotos + "  " + porsino + "%";

        // document.getElementById("img1").style.width = 5 * porsi;
        document.getElementById("img1").width = 5 * porsi;
        document.getElementById("img2").width = 5 * porno;
        document.getElementById("img3").width = 5 * porsino;
    }
}

function anade() {
    let pais = document.querySelector("#pais");
    // pais.options[1] = new Option(document.querySelector("#pais").value);
    let opcion = document.createElement("option");
    opcion.value = pais.value;
    opcion.innerHTML = pais.value;
    document.getElementById("country").appendChild(opcion);

}

// Cuadro de teto , con un boton , cuando le de a ok lo agregamos al option, si seleecciono una palabra tengo que agregarlo a otro option pero no se puede repetir

function anadeD() {
    let palabra = document.querySelector("#palabra");
    let opcion = document.createElement("option");
    opcion.value = palabra.value;
    opcion.innerHTML = palabra.value;
    document.getElementById("word").appendChild(opcion);
}

function anadeT() {
    let palabras2 = document.getElementById("word2");
    const LISTA = new Option(document.getElementById("word").value);

    for (let i = 0; i < palabras2.options.length; i++) {
        if (palabras2.options[i].value == document.getElementById("word").value) {
            alert("La palabra ya existe");
            return;
        }
    }
    palabras2.options.add(LISTA);
}



//*  EJERCICIO 5
let subCat = {
    pc: ["Procesador", "Memoria RAM", "Disco Duro"],
    teclado: ["Teclado mecánico", "Teclado estándar"],
    monitor: ["Monitor LED", "Monitor LCD"],
    mouse: ["Mouse óptico", "Mouse inalámbrico"]
};

function cargarSubCat() {
    // Obtener el valor de la categoría seleccionada en el <select id="categoria">
    let cat = document.getElementById("categoria").value;
    // Referencia al <select id="subCat"> donde se mostrarán las subcategorías
    let subCatSelect = document.getElementById("subCat");

    // Limpiar el desplegable de subcategorías y añadir la opción por defecto , se limpia para que no se muestren subcategorias de otras
    subCatSelect.innerHTML = '<option value="">Selecciona una subcategoria</option>';

    // Si se ha seleccionado una categoría (cat no está vacía)
    if (cat) {
        // Obtener el array de subcategorías correspondiente a la categoría elegida
        let subCategorias = subCat[cat];
        // Recorrer el array de subcategorías // el function sub es una funcion que se crea dentro del for each // el => es para hacer una funcion anonima pero tambien se puede poner function(nombre) { en vez de nombre => {
        subCategorias.forEach(function (sub) {
            // Crear un nuevo elemento <option>
            let opcion = document.createElement("option");
            opcion.value = sub.toLowerCase();
            // Texto visible para el usuario
            opcion.textContent = sub;
            // Añadir la opción al desplegable de subcategorías
            subCatSelect.appendChild(opcion);
        });
    }
}


// EJERCICIO 5.2
let listaSubCatD = [
    ["procesador", "memoria ram", "disco duro"],
    ["teclado mecánico", "teclado estándar"],
    ["monitor led", "monitor lcd"],
    ["mouse óptico", "mouse inalámbrico"]
]

function cargarSubCatD() {
    let categoriaD = document.getElementById("categoriaD").value;
    console.log(categoriaD);

    let subCatD = listaSubCatD[categoriaD];
    console.log(subCatD);
    
    let selectSubCatD = document.getElementById("subCatD");
    selectSubCatD.options.length = 0;

    for (let i = 0; i < subCatD.length; i++) {
        console.log(subCatD[i]);
        selectSubCatD.options.add(new Option(subCatD[i]));
    }
}


























