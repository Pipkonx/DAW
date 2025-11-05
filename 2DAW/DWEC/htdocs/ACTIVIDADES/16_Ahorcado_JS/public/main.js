let secreta = "";
let adivinadas = new Set();
let fallos = 0;
let maxfallos = 6;
let finalizado = false;

// Ruta absoluta del controlador PHP bajo Apache
// Si tu Apache escucha en otro puerto/host, ajusta esta URL.
const API_URL = "http://localhost/ACTIVIDADES/16_Ahorcado_JS/contorlador/juego.php";

function getParam(name) {
  const params = new URLSearchParams(window.location.search);
  return params.get(name);
}

function setText(id, text) {
  const el = document.getElementById(id);
  if (el) el.textContent = text;
}

// Juego
function iniciarJuego() {
  if (!secreta || secreta.length === 0) {
    // Fallback: elegir aleatoria del catálogo general
    const base = catalogoPalabras[1] || ["ahorcado"];
    secreta = base[Math.floor(Math.random() * base.length)].toLowerCase();
  }
  adivinadas = new Set();
  fallos = 0;
  finalizado = false;
  // set Text es para establecer el texto de un elemento html
  setText("maxfallos", String(maxfallos));
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
  enviarResultados(false);
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
  setText("fallos", String(fallos));
}

function establecerMensaje(msg) {
  setText("mensaje", msg);
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
  for (let i = 0; i < botones.length; i++) botones[i].disabled = true;
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

function verificarVictoria() {
  let ganado = true;
  for (let i = 0; i < secreta.length; i++) {
    if (!adivinadas.has(secreta[i])) { ganado = false; break; }
  }
  if (ganado) {
    finalizado = true;
    establecerMensaje("Ganaste!");
    deshabilitarAlfabeto();
    enviarResultados(true);
  }
}

// Persistencia en localStorage
function guardarResultado(login, result) {
  const key = `ahorcado:${login}:partidas`;
  const data = JSON.parse(localStorage.getItem(key) || "[]");
  data.unshift(result);
  localStorage.setItem(key, JSON.stringify(data));
}

function cargarResultados(login) {
  const key = `ahorcado:${login}:partidas`;
  return JSON.parse(localStorage.getItem(key) || "[]");
}

// Enviar resultados (sin servidor)
function enviarResultados(acertada) {
  const cfgEl = document.getElementById("game-config");
  const ds = (cfgEl && cfgEl.dataset) || {};
  //getParam es para obtener el valor de un parametro de
  const login = ds.login || getParam("login") || "Invitado";
  const idPalabra = ds.idPalabra ? parseInt(ds.idPalabra, 10) : 0;
  const acertadas = Array.from(adivinadas).filter((l) => secreta.includes(l)).length;
  const puntos = Math.max(0, secreta.length * 10 - fallos * 5);
  guardarResultado(login, {
    fecha: new Date().toISOString(),
    palabra: secreta,
    letras_acertadas: acertadas,
    letras_falladas: fallos,
    palabra_acertada: !!acertada,
    puntuacion_obtenida: puntos,
  });
  const body = new URLSearchParams({
    action: 'finalizar_json',
    login: login,
    id_palabra: String(idPalabra || 0),
    letras_acertadas: String(acertadas),
    letras_falladas: String(fallos),
    palabra_acertada: acertada ? '1' : '0',
    puntuacion_obtenida: String(puntos),
  }).toString();
  fetch(API_URL, {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body,
  })
    .then((r) => r.json())
    .then((data) => {
      const okMsg = data && data.ok ? 'Partida guardada' : (data && data.error ? data.error : 'Guardado completado');
      //encodeURIComponent es para la cadena de texto para que no se interprete como codigo
      const dest = `configurar.html?login=${encodeURIComponent(login)}&ok=${encodeURIComponent(okMsg)}`;
      window.location.href = dest;
    })
    .catch(() => {
      const dest = `configurar.html?login=${encodeURIComponent(login)}&ok=${encodeURIComponent('Guardado local completado')}`;
      window.location.href = dest;
    });
}

// Lectura de configuración para partida del URL o sessionStorage
function leerConfig() {
  const cfgEl = document.getElementById("game-config");
  if (!cfgEl) return;
  // Primero, intentar sessionStorage
  const raw = sessionStorage.getItem("ahorcado:config");
  let cfg = null;
  if (raw) {
    try { cfg = JSON.parse(raw); } catch { }
  }
  // Si no hay, leer de URL
  const login = getParam("login") || (cfg && cfg.login) || "Invitado";
  const palabra = getParam("palabra") || (cfg && cfg.palabra) || "";
  const mf = parseInt(getParam("maxfallos") || (cfg && cfg.maxfallos) || "6", 10);
  const idp = parseInt(getParam("id_palabra") || (cfg && cfg.id_palabra) || "0", 10);
  cfgEl.dataset.login = login;
  cfgEl.dataset.secreta = (palabra || "").toLowerCase();
  cfgEl.dataset.maxfallos = String(Number.isNaN(mf) ? 6 : mf);
  if (!Number.isNaN(idp) && idp > 0) cfgEl.dataset.idPalabra = String(idp);
  // Pintar usuario y maxfallos
  setText("usuario", login);
  setText("maxfallos", cfgEl.dataset.maxfallos);
  // Aplicar a estado
  secreta = cfgEl.dataset.secreta;
  const n = parseInt(cfgEl.dataset.maxfallos, 10);
  if (!Number.isNaN(n)) maxfallos = n;
}

// Páginas
function initGamePage() {
  const startBtn = document.getElementById("start");
  const endBtn = document.getElementById("end");
  const palabrasEl = document.getElementById("palabras");
  if (!startBtn || !endBtn || !palabrasEl) return;
  leerConfig();
  startBtn.addEventListener("click", iniciarJuego);
  endBtn.addEventListener("click", terminarJuego);
}

function initConfigPage() {
  const sel = document.getElementById("categoria");
  const form = document.getElementById("configForm");
  const usuarioEl = document.getElementById("usuario");
  const linkMis = document.getElementById("linkMisPartidas");
  if (!sel || !form) return;
  const login = getParam("login") || "Invitado";
  if (usuarioEl) usuarioEl.textContent = login;
  if (linkMis) linkMis.href = `mis_partidas.html?login=${encodeURIComponent(login)}`;
  // Poblar categorías
  sel.innerHTML = "";
  // opción por defecto
  const placeholder = document.createElement("option");
  placeholder.value = "";
  placeholder.textContent = "Seleccionar categoría";
  sel.appendChild(placeholder);
  fetch(`${API_URL}?action=categorias`)
    .then((r) => r.json())
    .then((data) => {
      sel.innerHTML = "";
      const ph = document.createElement("option");
      ph.value = "";
      ph.textContent = "Seleccionar categoría";
      sel.appendChild(ph);
      if (!Array.isArray(data) || data.length === 0) {
        sel.innerHTML = '<option value="">Sin categorías</option>';
        sel.disabled = true;
        return;
      }
      data.forEach((c) => {
        const opt = document.createElement("option");
        opt.value = String(c.id_categoria);
        opt.textContent = c.nombre_categoria;
        sel.appendChild(opt);
      });
      sel.disabled = false;
    })
    .catch(() => {
      sel.innerHTML = '<option value="">Error cargando categorías</option>';
      sel.disabled = true;
    });
  // Enviar configuración
  form.addEventListener("submit", (ev) => {
    ev.preventDefault();
    // El sel sirve para seleccionar la categoria
    const idCat = parseInt(sel.value, 10);
    const dif = document.getElementById("dificultad").value;
    if (!idCat || !dif) { setText("msg", "Selecciona categoría y dificultad"); return; }
    const body = new URLSearchParams({
      action: 'start_json',
      login: login,
      categoria: String(idCat),
      dificultad: dif,
    }).toString();
    fetch(API_URL, {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body,
    })
      .then((r) => r.json())
      .then((data) => {
        if (data && data.error) { setText("msg", data.error); return; }
        const palabra = data.palabra || "";
        const maxfallos = data.maxfallos || 6;
        const id_palabra = data.id_palabra || 0;
        sessionStorage.setItem("ahorcado:config", JSON.stringify({ login, palabra, maxfallos, id_palabra }));
        window.location.href = `partida.html?login=${encodeURIComponent(login)}`;
      })
      .catch(() => {
        setText("msg", "Error iniciando partida");
      });
  });
}

function initMisPartidasPage() {
  const cont = document.getElementById("contenedorPartidas");
  const volver = document.getElementById("volverConfig");
  if (!cont) return;
  const login = getParam("login") || "Invitado";
  setText("usuario", login);
  if (volver) volver.href = `configurar.html?login=${encodeURIComponent(login)}`;
  const partidas = cargarResultados(login);
  if (!partidas.length) {
    cont.innerHTML = '<p class="empty">Aún no tienes partidas registradas.</p>';
    return;
  }
  const table = document.createElement("table");
  table.innerHTML = `
    <thead>
      <tr>
        <th>Fecha</th>
        <th>Palabra</th>
        <th>Acertadas</th>
        <th>Falladas</th>
        <th>¿Acertada?</th>
        <th>Puntuación</th>
      </tr>
    </thead>
  `;
  const tbody = document.createElement("tbody");
  partidas.forEach((p) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${p.fecha}</td>
      <td>${p.palabra}</td>
      <td>${p.letras_acertadas}</td>
      <td>${p.letras_falladas}</td>
      <td>${p.palabra_acertada ? "Sí" : "No"}</td>
      <td>${p.puntuacion_obtenida}</td>
    `;
    tbody.appendChild(tr);
  });
  table.appendChild(tbody);
  cont.innerHTML = "";
  cont.appendChild(table);
}

// Usuarios en localStorage
function getUsers() {
  return JSON.parse(localStorage.getItem("ahorcado:users") || "{}");
}
function setUsers(users) {
  localStorage.setItem("ahorcado:users", JSON.stringify(users));
}

function initLoginPage() {
  const form = document.getElementById("loginForm");
  const msg = document.getElementById("msg");
  if (!form) return;
  const ok = getParam("ok");
  const login = getParam("login");
  if (ok) msg.textContent = `${ok}${login ? " — Usuario: " + login : ""}`;
  form.addEventListener("submit", (ev) => {
    ev.preventDefault();
    const login = document.getElementById("login").value.trim();
    const password = document.getElementById("password").value;
    if (!login || !password) { msg.textContent = "Credenciales inválidas"; return; }
    const users = getUsers();
    if (!users[login]) { msg.textContent = "Usuario no encontrado"; return; }
    if (users[login].password !== password) { msg.textContent = "Contraseña incorrecta"; return; }
    window.location.href = `../juego/configurar.html?login=${encodeURIComponent(login)}`;
  });
}

function initRegisterPage() {
  const form = document.getElementById("registerForm");
  const msg = document.getElementById("msg");
  if (!form) return;
  form.addEventListener("submit", (ev) => {
    ev.preventDefault();
    const login = document.getElementById("login").value.trim();
    const password = document.getElementById("password").value;
    const confirm = document.getElementById("confirm").value;
    if (!login || !password || !confirm) { msg.textContent = "Completa todos los campos"; return; }
    if (password !== confirm) { msg.textContent = "Las contraseñas no coinciden"; return; }
    const users = getUsers();
    if (users[login]) { msg.textContent = "El usuario ya existe"; return; }
    users[login] = { password };
    setUsers(users);
    window.location.href = `login.html?ok=Registro+completado&login=${encodeURIComponent(login)}`;
  });
}

// Inicialización global
function init() {
  initLoginPage();
  initRegisterPage();
  initConfigPage();
  initGamePage();
  initMisPartidasPage();
}

if (document.readyState !== "loading") init();
else document.addEventListener("DOMContentLoaded", init);
