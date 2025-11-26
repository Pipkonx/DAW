<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partida</title>
    <?php
    $login = isset($_GET['login']);
    $idPalabra = isset($_GET['id_palabra']);
    $palabra = isset($_GET['palabra']);
    $maxfallos = isset($_GET['maxfallos']) ? intval($_GET['maxfallos']) : 6;
    ?>
</head>

<body onload="renderizarAlfabeto()">
    <h1>Partida de Ahorcado</h1>
    <p>Usuario: <?= $login ?></p>

    <div>
        <p>Fallos: <span id="fallos">0</span> / <span id="maxfallos"><?= $maxfallos ?></span></p>
        <p id="palabras">_ _ _ _ _</p>
        <div id="teclado"></div>
        <p id="mensaje"></p>
        <button id="start">Empezar</button>
        <button id="end">Terminar</button>

    </div>

    <form id="finalForm" action="../../contorlador/juego.php" method="post" style="display:none">
        <input type="hidden" name="action" value="finalizar">
        <input type="hidden" name="login" value="<?= $login ?>">
        <input type="hidden" name="id_palabra" value="<?= $idPalabra ?>">
        <input type="hidden" name="letras_acertadas" id="f_acertadas" value="0">
        <input type="hidden" name="letras_falladas" id="f_falladas" value="0">
        <input type="hidden" name="palabra_acertada" id="f_acertada" value="0">
        <input type="hidden" name="puntuacion_obtenida" id="f_puntos" value="0">
    </form>
    <div id="game-config"
        data-secreta="<?= strtolower($palabra) ?>"
        data-maxfallos="<?= $maxfallos ?>"
        data-login="<?= $login ?>"
        data-id-palabra="<?= $idPalabra ?>"></div>
    <script src="../public/main.js"></script>
</body>

</html>