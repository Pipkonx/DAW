// Lógica común del juego y de configuración centralizada aquí
const palabrasDisponibles = [
  "javascript", "html", "css", "programar", "codigo", "ahorcado", "frontend", "backend", "variable", "funcion", "array",
];

let palabraSecreta = "";
let letrasAdivinadas = new Set();
let numeroFallos = 0;
let maximoFallos = 6;
let juegoFinalizado = false;

/**
 * Inicia una nueva partida del juego del ahorcado.
 * Reinicia el estado del juego, selecciona una nueva palabra secreta si no hay una configurada,
 * y actualiza la interfaz de usuario.
 */
function iniciarJuego() {
  // Si hay configuración desde el servidor, respetarla; si no, elegir aleatoria
  if (!palabraSecreta || palabraSecreta.length === 0) {
    palabraSecreta = palabrasDisponibles[Math.floor(Math.random() * palabrasDisponibles.length)];
  }
  letrasAdivinadas = new Set();
  numeroFallos = 0;
  juegoFinalizado = false;
  const elMax = document.getElementById("maxfallos");
  if (elMax) elMax.textContent = String(maximoFallos);
  actualizarPalabra();
  actualizarFallos();
  establecerMensaje("");
  renderizarAlfabeto();
}

/**
 * Termina la partida actual del juego.
 * Establece el estado del juego como finalizado, revela la palabra secreta,
 * muestra un mensaje de fin de partida y deshabilita el teclado.
 */
function terminarJuego() {
  juegoFinalizado = true;
  revelarPalabra();
  establecerMensaje("Partida terminada.");
  deshabilitarAlfabeto();
}

/**
 * Actualiza la representación visual de la palabra secreta en la interfaz de usuario.
 * Muestra las letras adivinadas y guiones bajos para las letras no adivinadas.
 * También verifica si el jugador ha ganado después de cada actualización.
 */
function actualizarPalabra() {
  let display = "";
  for (let i = 0; i < palabraSecreta.length; i++) {
    //has es para verificar si la letra esta
    if (letrasAdivinadas.has(palabraSecreta[i])) {
      display += palabraSecreta[i];
    } else {
      display += "_";
    }
    if (i < secreta.length - 1) display += " ";
  }
  document.getElementById("palabras").textContent = display;
  verificarVictoria();
}

/**
 * Revela completamente la palabra secreta en la interfaz de usuario.
 * Se utiliza típicamente al final de la partida para mostrar la solución.
 */
function revelarPalabra() {
  let mostrar = "";
  for (let i = 0; i < palabraSecreta.length; i++) {
    mostrar += palabraSecreta[i];
    if (i < secreta.length - 1) mostrar += " ";
  }
  const el = document.getElementById("palabras");
  if (el) el.textContent = mostrar;
}

/**
 * Actualiza el contador de fallos en la interfaz de usuario.
 */
function actualizarFallos() {
  const el = document.getElementById("fallos");
  if (el) el.textContent = String(numeroFallos);
}

/**
 * Establece un mensaje en la interfaz de usuario para informar al jugador.
 * @param {string} msg - El mensaje a mostrar.
 */
function establecerMensaje(msg) {
  document.getElementById("mensaje").textContent = msg;
}

/**
 * Renderiza el teclado de letras en la interfaz de usuario.
 * Crea botones para cada letra del alfabeto y les asigna un evento de clic.
 */
function renderizarAlfabeto() {
  const cont = document.getElementById("teclado");
  cont.innerHTML = "";
  const letras = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "Ñ", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
  for (let i = 0; i < letras.length; i++) {
    const btn = document.createElement("button");
    btn.textContent = letras[i];
    btn.className = "letra";
    btn.disabled = juegoFinalizado;
    btn.addEventListener("click", () => elegir(letras[i].toLowerCase()));
    cont.appendChild(btn);
  }
}

/**
 * Deshabilita todos los botones del teclado.
 * Se utiliza cuando la partida ha terminado para evitar más interacciones.
 */
function deshabilitarAlfabeto() {
  const botones = document.querySelectorAll(".letra");
  for (let i = 0; i < botones.length; i++) {
    botones[i].disabled = true;
  }
}

/**
 * Procesa la elección de una letra por parte del jugador.
 * Verifica si la letra ha sido adivinada o si la partida ha terminado.
 * Actualiza el estado del juego (fallos, letras adivinadas) y la interfaz de usuario.
 * @param {string} letra - La letra elegida por el jugador.
 */
function elegir(letra) {
    if (juegoFinalizado || letrasAdivinadas.has(letra)) return;
  letrasAdivinadas.add(letra);
  if (palabraSecreta.indexOf(letra) !== -1) {
    actualizarPalabra();
    establecerMensaje("Bien");
  } else {
    numeroFallos++;
    actualizarFallos();
    establecerMensaje("Letra incorrecta");
    if (numeroFallos >= maximoFallos) {
      juegoFinalizado = true;
      establecerMensaje("Has perdido, la palabra era: " + palabraSecreta);
      revelarPalabra();
      deshabilitarAlfabeto();
    }
  }
}

/**
 * Verifica si el jugador ha ganado la partida.
 * Si todas las letras de la palabra secreta han sido adivinadas, el juego termina con victoria.
 */
function verificarVictoria() {
  let ganado = true;
  for (let i = 0; i < palabraSecreta.length; i++) {
    if (!letrasAdivinadas.has(palabraSecreta[i])) {
      ganado = false;
      break;
    }
  }
  if (ganado) {
    juegoFinalizado = true;
    establecerMensaje("¡Ganaste!");
    deshabilitarAlfabeto();
    enviarResultados(true);
  }
}

function establecerMensaje(msg) {
  const el = document.getElementById("mensaje");
  if (el) el.textContent = msg;
}

function renderizarAlfabeto() {
  const cont = document.getElementById("teclado");
  if (!cont) return;
  cont.innerHTML = "";
  const letras = [
    "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "Ñ", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z",
  ];
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
      enviarResultados(false);
    }
  }
}

function actualizarPalabra() {
  let display = "";
  for (let i = 0; i < secreta.length; i++) {
    if (adivinadas.has(secreta[i])) {
      display += secreta[i];
    } else {
      display += "_";
    }
    if (i < secreta.length - 1) display += " ";
  }
  const el = document.getElementById("palabras");
  if (el) el.textContent = display;
  verificarVictoria();
}

function terminarJuego() {
  juegoFinalizado = true;
  revelarPalabra();
  establecerMensaje("Partida terminada.");
  deshabilitarAlfabeto();
  enviarResultados(false);
}

/**
 * Envía los resultados de la partida a un formulario oculto.
 * Calcula las letras acertadas, fallos y puntos obtenidos.
 * @param {boolean} acertada - Indica si la partida fue ganada (true) o perdida (false).
 */
function enviarResultados(acertada) {
  const form = document.getElementById("finalForm");
  if (!form) return; // En algunas vistas no hay envío de resultados
  const acertadas = Array.from(letrasAdivinadas).filter((l) => palabraSecreta.includes(l)).length; // aproximado
  const fAcertadas = document.getElementById("f_acertadas");
  const fFalladas = document.getElementById("f_falladas");
  const fAcertada = document.getElementById("f_acertada");
  const fPuntos = document.getElementById("f_puntos");
  if (fAcertadas) fAcertadas.value = String(acertadas);
  if (fFalladas) fFalladas.value = String(numeroFallos);
  if (fAcertada) fAcertada.value = acertada ? "1" : "0";
  const puntos = Math.max(0, palabraSecreta.length * 10 - numeroFallos * 5);
  if (fPuntos) fPuntos.value = String(puntos);
  form.submit();
}

/**
 * Lee la configuración del juego desde los atributos `data-` de un elemento HTML.
 * Permite configurar la palabra secreta y el número máximo de fallos.
 */
function leerConfig() {
  const cfg = document.getElementById("game-config");
  if (!cfg) return;
  const dataset = cfg.dataset || {};
  if (dataset.secreta) {
    palabraSecreta = String(dataset.secreta).toLowerCase();
  }
  if (dataset.maxfallos) {
    const n = parseInt(dataset.maxfallos, 10);
    if (!Number.isNaN(n)) maximoFallos = n;
  }
}

/**
 * Inicializa la página del juego.
 * Configura los manejadores de eventos para los botones de iniciar y terminar partida.
 */
function initGamePage() {
  const startBtn = document.getElementById("start");
  const endBtn = document.getElementById("end");
  const palabrasEl = document.getElementById("palabras");
  if (!startBtn || !endBtn || !palabrasEl) return; // No es una página de juego

  leerConfig();

  startBtn.addEventListener("click", iniciarJuego);
  endBtn.addEventListener("click", terminarJuego);
}

/**
 * Inicializa la página de configuración.
 * Carga dinámicamente las categorías desde el servidor y las muestra en un selector.
 */
function initConfigPage() {
  const sel = document.getElementById("categoria");
  if (!sel) return; // No es la página de configurar
  fetch("../../contorlador/juego.php?action=categorias")
    .then((r) => r.json())
    .then((data) => {
      console.log("Categories data:", data);
      sel.innerHTML = "";
      if (!Array.isArray(data) || data.length === 0) {
        sel.innerHTML = '<option value="">Sin categorías</option>';
        sel.disabled = true;
        return;
      }
      data.forEach((c) => {
        const opt = document.createElement("option");
        opt.value = c.id_categoria;
        opt.textContent = c.nombre_categoria;
        sel.appendChild(opt);
      });
    })
    .catch(() => {
      sel.innerHTML = '<option value="">Error cargando categorías</option>';
      sel.disabled = true;
    });
}

// Inicialización cuando el DOM esté listo
if (document.readyState !== "loading") {
  initConfigPage();
  initGamePage();
} else {
  document.addEventListener("DOMContentLoaded", () => {
    initConfigPage();
    initGamePage();
  });
}
