var titulo;

function muestraTitulo() {
    titulo = document.getElementById("titulo");
    // alert(titulo.innerHTML)
    // console.log(titulo.innerHTML)
    titulo.innerHTML = "Hola Mundo!";
}

function contador() {
    cuenta = document.getElementById("cuenta");
    cuenta.innerHTML = parseInt(cuenta.innerHTML) + 1;
}

function parrafos(){
    parrafos = document.getElementsByTagName("p");
    parrafo = prompt("Parrafo a cambiar [0-2]: ");
    parrafos[parrafo].innerHTML = "PARRAFO CAMBIADO";

}