const palabras = [
    "javascript", "html", "css", "programar", "codigo", "ahorcado", "frontend", "backend", "variable", "funcion", "array"
];
let secreta = "";
let adivinadas = new Set();
let fallos = 0;
const maxfallos = 6;
let finalizado = false;

function iniciarJuego() {
    secreta = palabras[Math.floor(Math.random() * palabras.length)];
    adivinadas = new Set();
    fallos = 0;
    finalizado = false;
    document.getElementById("maxfallos").textContent = String(maxfallos);
    actualizarPalabra();
    actualizarFallos();
    establecerMensaje("");
    renderizarAlfabeto();
}

function terminarJuego() {
    finalizado = true;
    revelarPalabra();
    establecerMensaje("Partida terminada.");
    deshabilitarAlfabeto();
}

function actualizarPalabra() {
    let display = "";
    for (let i = 0; i < secreta.length; i++) {
        //has es para verificar si la letra esta
        if (adivinadas.has(secreta[i])) {
            display += secreta[i];
        } else {
            display += "_";
        }
        if (i < secreta.length - 1) display += " ";
    }
    document.getElementById("palabras").textContent = display;
    verificarVictoria();
}

function revelarPalabra() {
    let mostrar = "";
    for (let i = 0; i < secreta.length; i++) {
        mostrar += secreta[i];
        if (i < secreta.length - 1) mostrar += " ";
    }
    document.getElementById("palabras").textContent = mostrar;
}

function actualizarFallos() {
    document.getElementById("fallos").textContent = String(fallos);
}

function establecerMensaje(msg) {
    document.getElementById("mensaje").textContent = msg;
}

function renderizarAlfabeto() {
    const cont = document.getElementById("teclado");
    cont.innerHTML = "";
    const letras = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "Ñ", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
    for (let i = 0; i < letras.length; i++) {
        const btn = document.createElement("button");
        btn.textContent = letras[i];
        btn.className = "letra";
        btn.disabled = finalizado;
        btn.addEventListener("click", () => elegir(letras[i].toLowerCase()));
        cont.appendChild(btn);
    }
}

function deshabilitarAlfabeto() {
    const botones = document.querySelectorAll(".letra");
    for (let i = 0; i < botones.length; i++) {
        botones[i].disabled = true;
    }
}

function elegir(letra) {
    if (finalizado || adivinadas.has(letra)) return;
    adivinadas.add(letra);
    if (secreta.indexOf(letra) !== -1) {
        actualizarPalabra();
        establecerMensaje("Bien");
    } else {
        fallos++;
        actualizarFallos();
        establecerMensaje("Letra incorrecta");
        if (fallos >= maxfallos) {
            finalizado = true;
            establecerMensaje("Has perdido, la palabra era: " + secreta);
            revelarPalabra();
            deshabilitarAlfabeto();
        }
    }
}

function verificarVictoria() {
    let ganado = true;
    for (let i = 0; i < secreta.length; i++) {
        if (!adivinadas.has(secreta[i])) {
            ganado = false;
            break;
        }
    }
    if (ganado) {
        finalizado = true;
        establecerMensaje("Ganastes");
        deshabilitarAlfabeto();
    }
}

document.getElementById("start").addEventListener("click", iniciarJuego);
document.getElementById("end").addEventListener("click", terminarJuego);

iniciarJuego();
