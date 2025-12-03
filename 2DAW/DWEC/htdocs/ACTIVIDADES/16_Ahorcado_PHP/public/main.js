let palabraSecretaActual = "";
let letrasAdivinadasUsuario = new Set();
let cantidadFallos = 0;
let maximoIntentosFallidos = 6;
let juegoEnCurso = false;


function iniciarJuego() {
  letrasAdivinadasUsuario = new Set();
  cantidadFallos = 0;
  juegoEnCurso = false;
  const elementoMaximoFallos = document.getElementById("maxfallos");
  if (elementoMaximoFallos) elementoMaximoFallos.textContent = String(maximoIntentosFallidos);
  actualizarPalabraMostrada();
  actualizarContadorFallos();
  establecerMensajeJuego("");
  renderizarTecladoAlfabetico();
  document.getElementById("start").classList.add("hidden"); // Ocultar el botón de inicio
}

function terminarJuego() {
  juegoEnCurso = true;
  revelarPalabraCompleta();
  establecerMensajeJuego("Partida terminada.");
  deshabilitarTecladoAlfabetico();
  document.getElementById("start").classList.remove("hidden"); // Mostrar el botón de inicio
}

function actualizarPalabraMostrada() {
  let palabraMostrada = "";
  for (let i = 0; i < palabraSecretaActual.length; i++) {
    // Si la letra actual de la palabra secreta ha sido adivinada
    if (letrasAdivinadasUsuario.has(palabraSecretaActual[i])) {
      palabraMostrada += palabraSecretaActual[i];
    } else {
      palabraMostrada += "_"; // Si no, mostrar un guion bajo
    }
    if (i < palabraSecretaActual.length - 1) palabraMostrada += " ";
  }
  document.getElementById("palabras").textContent = palabraMostrada;
  verificarVictoria();
}

function revelarPalabraCompleta() {
  let palabraVisible = "";
  for (let i = 0; i < palabraSecretaActual.length; i++) {
    palabraVisible += palabraSecretaActual[i];
    if (i < palabraSecretaActual.length - 1) palabraVisible += " ";
  }
  const elementoPalabra = document.getElementById("palabras");
  if (elementoPalabra) elementoPalabra.textContent = palabraVisible;
}

function actualizarContadorFallos() {
  const elementoFallos = document.getElementById("fallos");
  if (elementoFallos) elementoFallos.textContent = String(cantidadFallos);
}

function establecerMensajeJuego(mensaje) {
  document.getElementById("mensaje").textContent = mensaje;
}

function renderizarTecladoAlfabetico() {
  const contenedorTeclado = document.getElementById("teclado");
  contenedorTeclado.innerHTML = "";
  const letrasAlfabeto = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "Ñ", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
  for (let i = 0; i < letrasAlfabeto.length; i++) {
    const botonLetra = document.createElement("button");
    botonLetra.textContent = letrasAlfabeto[i];
    botonLetra.className = "letra";
    botonLetra.addEventListener("click", (evento) => seleccionarLetra(letrasAlfabeto[i].toLowerCase(), evento.target));
    contenedorTeclado.appendChild(botonLetra);
  }
}

function deshabilitarTecladoAlfabetico() {
  const botonesLetra = document.querySelectorAll(".letra");
  for (let i = 0; i < botonesLetra.length; i++) {
    botonesLetra[i].disabled = true;
  }
}

function seleccionarLetra(letra, elementoBoton) {
  if (juegoEnCurso || letrasAdivinadasUsuario.has(letra)) return;
  letrasAdivinadasUsuario.add(letra);
  if (elementoBoton) {
    // deshabilitar las letras que vamos clickeando para evitar que se intenten volver a clickear
    elementoBoton.disabled = true;
  }
  if (palabraSecretaActual.indexOf(letra) !== -1) {
    actualizarPalabraMostrada();
    establecerMensajeJuego("Bien");
  } else {
    cantidadFallos++;
    actualizarContadorFallos();
    establecerMensajeJuego("Letra incorrecta");
    if (cantidadFallos >= maximoIntentosFallidos) {
      juegoEnCurso = true;
      establecerMensajeJuego("Has perdido, la palabra era: " + palabraSecretaActual);
      revelarPalabraCompleta();
      deshabilitarTecladoAlfabetico();
    }
  }
}

function verificarVictoria() {
  let haGanado = true;
  for (let i = 0; i < palabraSecretaActual.length; i++) {
    if (!letrasAdivinadasUsuario.has(palabraSecretaActual[i])) {
      haGanado = false;
      break;
    }
  }
  if (haGanado) {
    juegoEnCurso = true;
    establecerMensajeJuego("¡Ganaste!");
    deshabilitarTecladoAlfabetico();
    enviarResultados(true);
  }
}

function finalizarJuego() {
  juegoEnCurso = true;
  revelarPalabraCompleta();
  establecerMensajeJuego("Partida terminada.");
  deshabilitarTecladoAlfabetico();
  enviarResultados(false);
}

function enviarResultados(acertada) {
    const formularioFinal = document.getElementById("finalForm");
    if (!formularioFinal) return; // En algunas vistas no hay envío de resultados

    // Contar las letras acertadas que están en la palabra secreta
    const cantidadLetrasAcertadas = Array.from(letrasAdivinadas).filter((letra) => palabraSecreta.includes(letra)).length;

    const campoAcertadas = document.getElementById("f_acertadas");
    const campoFalladas = document.getElementById("f_falladas");
    const campoPalabraAcertada = document.getElementById("f_acertada");
    const campoPuntos = document.getElementById("f_puntos");

    if (campoAcertadas) campoAcertadas.value = String(cantidadLetrasAcertadas);
    if (campoFalladas) campoFalladas.value = String(numeroFallos);
    if (campoPalabraAcertada) campoPalabraAcertada.value = acertada ? "1" : "0";

    const puntosCalculados = Math.max(0, palabraSecreta.length * 10 - numeroFallos * 5);
    if (campoPuntos) campoPuntos.value = String(puntosCalculados);

    formularioFinal.submit();
  }

function leerConfiguracion() {
  const configuracionJuego = document.getElementById("game-config");
  // Si no se encuentra el elemento de configuración, salimos de la función
  if (!configuracionJuego) return;
  const datosConfiguracion = configuracionJuego.dataset || {};
  if (datosConfiguracion.secreta) {
    palabraSecretaActual = String(datosConfiguracion.secreta).toLowerCase();
  }
  if (datosConfiguracion.maxfallos) {
    const numeroMaximoFallos = parseInt(datosConfiguracion.maxfallos, 10);
    if (!isNaN(numeroMaximoFallos)) maximoIntentosFallidos = numeroMaximoFallos;
  }
}

function inicializarPaginaJuego() {
    const botonInicio = document.getElementById("start");
    const elementoPalabras = document.getElementById("palabras");
    if (!botonInicio || !elementoPalabras) return; // No es una página de juego

    leerConfiguracion();
    iniciarJuego(); // Iniciar el juego automáticamente al cargar la página
    botonInicio.classList.add("hidden"); // Asegurarse de que el botón esté oculto al cargar la página
}




inicializarPaginaJuego();
