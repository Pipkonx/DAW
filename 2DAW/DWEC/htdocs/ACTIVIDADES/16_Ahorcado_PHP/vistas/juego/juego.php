<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ahorcado básico</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <main class="card">
    <h1>Ahorcado</h1>
    <div id="palabra" aria-label="Palabra oculta"></div>
    <div id="status">
      <div>Fallos: <span id="fallos">0</span> / <span id="maximoFallos">6</span></div>
    </div>
    <div id="teclado" aria-label="Teclado de letras"></div>
    <div id="mensaje"></div>
    <div class="actions">
      <button id="start">Nueva partida</button>
      <button id="end">Terminar partida</button>
    </div>
  </main>
</body>
<script src="../public/main.js"></script>

</html>