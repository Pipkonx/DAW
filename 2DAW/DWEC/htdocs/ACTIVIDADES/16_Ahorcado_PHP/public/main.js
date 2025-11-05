// Lógica común del juego y de configuración centralizada aquí
const palabras = [
  "javascript",
  "html",
  "css",
  "programar",
  "codigo",
  "ahorcado",
  "frontend",
  "backend",
  "variable",
  "funcion",
  "array",
];

let secreta = "";
let adivinadas = new Set();
let fallos = 0;
let maxfallos = 6;
let finalizado = false;

function iniciarJuego() {
  // Si hay configuración desde el servidor, respetarla; si no, elegir aleatoria
  if (!secreta || secreta.length === 0) {
    secreta = palabras[Math.floor(Math.random() * palabras.length)];
  }
  adivinadas = new Set();
  fallos = 0;
  finalizado = false;
  const elMax = document.getElementById("maxfallos");
  if (elMax) elMax.textContent = String(maxfallos);
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
  const el = document.getElementById("palabras");
  if (el) el.textContent = mostrar;
}

function actualizarFallos() {
  const el = document.getElementById("fallos");
  if (el) el.textContent = String(fallos);
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
    "A",
    "B",
    "C",
    "D",
    "E",
    "F",
    "G",
    "H",
    "I",
    "J",
    "K",
    "L",
    "M",
    "N",
    "Ñ",
    "O",
    "P",
    "Q",
    "R",
    "S",
    "T",
    "U",
    "V",
    "W",
    "X",
    "Y",
    "Z",
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
  finalizado = true;
  revelarPalabra();
  establecerMensaje("Partida terminada.");
  deshabilitarAlfabeto();
  enviarResultados(false);
}

function enviarResultados(acertada) {
  const form = document.getElementById("finalForm");
  if (!form) return; // En algunas vistas no hay envío de resultados
  const acertadas = Array.from(adivinadas).filter((l) => secreta.includes(l)).length; // aproximado
  const fAcertadas = document.getElementById("f_acertadas");
  const fFalladas = document.getElementById("f_falladas");
  const fAcertada = document.getElementById("f_acertada");
  const fPuntos = document.getElementById("f_puntos");
  if (fAcertadas) fAcertadas.value = String(acertadas);
  if (fFalladas) fFalladas.value = String(fallos);
  if (fAcertada) fAcertada.value = acertada ? "1" : "0";
  const puntos = Math.max(0, secreta.length * 10 - fallos * 5);
  if (fPuntos) fPuntos.value = String(puntos);
  form.submit();
}

function leerConfig() {
  const cfg = document.getElementById("game-config");
  if (!cfg) return;
  const dataset = cfg.dataset || {};
  if (dataset.secreta) {
    secreta = String(dataset.secreta).toLowerCase();
  }
  if (dataset.maxfallos) {
    const n = parseInt(dataset.maxfallos, 10);
    if (!Number.isNaN(n)) maxfallos = n;
  }
}

function initGamePage() {
  const startBtn = document.getElementById("start");
  const endBtn = document.getElementById("end");
  const palabrasEl = document.getElementById("palabras");
  if (!startBtn || !endBtn || !palabrasEl) return; // No es una página de juego

  leerConfig();

  startBtn.addEventListener("click", iniciarJuego);
  endBtn.addEventListener("click", terminarJuego);
}

function initConfigPage() {
  const sel = document.getElementById("categoria");
  if (!sel) return; // No es la página de configurar
  fetch("../../contorlador/juego.php?action=categorias")
    .then((r) => r.json())
    .then((data) => {
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
