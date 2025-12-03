<?php
$tituloPagina = "Ahorcado básico";
require_once __DIR__ . '/../comun/V_header.php';
?>

<body>
  <main class="card">
    <h1>Ahorcado</h1>
    <div class="centered-content">
      <div id="palabra" aria-label="Palabra oculta"></div>
      <div id="status">
        <div>Fallos: <span id="numeroFallos">0</span> / <span id="maximoFallos">6</span></div>
      </div>
    </div>
    <div id="teclado" aria-label="Teclado de letras"></div>
    <div id="mensaje"></div>
    <div class="actions">
      <button id="start">Nueva partida</button>
    </div>
  </main>
</body>
<script src="../public/main.js"></script>

</html>