<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ahorcado básico</title>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../../public/style.css">
</head>

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