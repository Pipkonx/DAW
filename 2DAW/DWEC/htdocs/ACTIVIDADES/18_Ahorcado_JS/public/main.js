// JS mínimo para jugar al ahorcado usando fetch y HTML sencillo

let palabraSecreta = "";
let letrasAdivinadas = [];
let fallos = 0;
let maximoFallos = 6;
let terminado = false;

function contiene(arr, v) {
  for (let i = 0; i < arr.length; i++) {
    if (arr[i] === v) return true;
  }
  return false;
}

function contieneEnPalabra(palabra, letra) {
  for (let i = 0; i < palabra.length; i++) {
    if (palabra[i] === letra) return true;
  }
  return false;
}

function mostrarPalabra() {
  const contenedor = document.getElementById("palabra");
  let texto = "";
  for (let i = 0; i < palabraSecreta.length; i++) {
    const l = palabraSecreta[i];
    texto += contiene(letrasAdivinadas, l) ? l : "_";
    if (i < palabraSecreta.length - 1) texto += " ";
  }
  contenedor.textContent = texto;
}

function actualizarEstado() {
  document.getElementById("numeroFallos").textContent = String(fallos);
  document.getElementById("maximoFallos").textContent = String(maximoFallos);
}

function setMensaje(m) {
  document.getElementById("mensaje").textContent = m;
}

function deshabilitarTeclado() {
  const botones = document.querySelectorAll(".letra");
  for (let i = 0; i < botones.length; i++) {
    botones[i].disabled = true;
  }
}

function renderTeclado() {
  const cont = document.getElementById("teclado");
  cont.innerHTML = "";
  const letras = ["A","B","C","D","E","F","G","H","I","J","K","L","M","N","Ñ","O","P","Q","R","S","T","U","V","W","X","Y","Z"];
  for (let i = 0; i < letras.length; i++) {
    const L = letras[i];
    const b = document.createElement("button");
    b.textContent = L;
    b.className = "letra";
    b.addEventListener("click", function() { elegirLetra(L.toLowerCase(), b); });
    cont.appendChild(b);
  }
}

function elegirLetra(letra, boton) {
  if (terminado || contiene(letrasAdivinadas, letra)) return;
  letrasAdivinadas[letrasAdivinadas.length] = letra;
  if (boton) boton.disabled = true;

  if (contieneEnPalabra(palabraSecreta, letra)) {
    mostrarPalabra();
    setMensaje("¡Bien!");
    comprobarVictoria();
  } else {
    fallos++;
    actualizarEstado();
    setMensaje("Letra incorrecta");
    comprobarDerrota();
  }
}

function comprobarVictoria() {
  for (let i = 0; i < palabraSecreta.length; i++) {
    if (!contiene(letrasAdivinadas, palabraSecreta[i])) return;
  }
  terminado = true;
  setMensaje("¡Ganaste!");
  deshabilitarTeclado();
}

function comprobarDerrota() {
  if (fallos >= maximoFallos) {
    terminado = true;
    setMensaje("Has perdido, la palabra era: " + palabraSecreta);
    deshabilitarTeclado();
    let visible = "";
    for (let i = 0; i < palabraSecreta.length; i++) {
      visible += palabraSecreta[i];
      if (i < palabraSecreta.length - 1) visible += " ";
    }
    document.getElementById("palabra").textContent = visible;
  }
}

async function cargarNuevaPalabra() {
  try {
    const resp = await fetch("../contorlador/C_juego.php?action=palabra");
    const data = await resp.json();
    if (data && data.palabra) {
      palabraSecreta = String(data.palabra).toLowerCase();
      letrasAdivinadas = [];
      fallos = 0;
      terminado = false;
      maximoFallos = 6;
      actualizarEstado();
      mostrarPalabra();
      renderTeclado();
      setMensaje("");
    } else {
      setMensaje("No se pudo cargar palabra");
    }
  } catch (e) {
    setMensaje("Error de red con fetch");
    console.error(e);
  }
}

function iniciar() {
  const startBtn = document.getElementById("start");
  startBtn.addEventListener("click", cargarNuevaPalabra);
  cargarNuevaPalabra();
}

document.addEventListener("DOMContentLoaded", iniciar);
