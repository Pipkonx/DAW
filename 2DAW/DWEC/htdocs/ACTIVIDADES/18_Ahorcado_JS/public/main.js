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
    const configuracionJuego = document.getElementById("game-config");
    if (!configuracionJuego) return; // No hay configuración de juego disponible

    // Obtener parámetros de juego desde data attributes
    const datosConfiguracion = configuracionJuego.dataset || {};
    const nombreUsuario = datosConfiguracion.usuario;
    const idPalabraSecreta = datosConfiguracion.idPalabra;

    // Corregir nombres de variables y calcular valores
    const cantidadLetrasAcertadas = Array.from(letrasAdivinadasUsuario).filter((letra) => palabraSecretaActual.includes(letra)).length;
    const puntosCalculados = Math.max(0, palabraSecretaActual.length * 10 - cantidadFallos * 5);

    // Preparar datos para envío AJAX
    const datosResultados = new FormData();
    datosResultados.append("f_acertadas", String(cantidadLetrasAcertadas));
    datosResultados.append("f_falladas", String(cantidadFallos));
    datosResultados.append("f_acertada", acertada ? "1" : "0");
    datosResultados.append("f_puntos", String(puntosCalculados));
    datosResultados.append("id_palabra", idPalabraSecreta);
    datosResultados.append("login", nombreUsuario);

    // Enviar resultados via AJAX
    fetch("../../contorlador/C_juego.php?action=finalizar", {
        method: "POST",
        body: datosResultados,
        credentials: "include" // Mantener sesión del usuario
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            establecerMensajeJuego(data.message);
            // Redirigir a página de configuración después de 2 segundos
            setTimeout(() => window.location = `V_configurar.php?login=${nombreUsuario}`, 2000);
        } else {
            establecerMensajeJuego(`Error al guardar resultados: ${data.message}`);
        }
    })
    .catch(error => {
        console.error("Error al enviar resultados:", error);
        establecerMensajeJuego("Ocurrió un error al guardar los resultados de la partida.");
    });
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
