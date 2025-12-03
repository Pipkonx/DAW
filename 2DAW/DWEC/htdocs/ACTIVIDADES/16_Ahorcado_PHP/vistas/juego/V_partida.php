<!DOCTYPE html>
<html lang="es">

<?php
    $tituloPagina = "Partida";
    require_once __DIR__ . '/../comun/V_header.php';
    $nombreUsuario = $_GET['login'] ?? 'Invitado';
    $idPalabraSecreta = $_GET['id_palabra'] ?? '';
    $palabraSecreta = $_GET['palabra'] ?? '';
    $maximoFallos = isset($_GET['maxfallos']) ? $_GET['maxfallos'] : 6;
?>

<body>
    <h1>Partida de Ahorcado</h1>
    <div class="flex-container">
        <a href="V_configurar.php?login=<?= urlencode($nombreUsuario) ">Volver a Configurar Juego</a>
        <a href="../../contorlador/C_juego.php?action=logout">Cerrar Sesión</a>
    </div>
    <p>Usuario: <?= $nombreUsuario ?></p>

    <div>
        <div class="game-info-container">
            <p>Fallos: <span id="fallos">0</span> / <span id="maxfallos"><?= $maximoFallos ?></span></p>
            <p id="palabras">_ _ _ _ _</p>
            <p id="mensaje"></p>
        </div>
        <div id="teclado"></div>
        <button id="start">Empezar</button>

    </div>

    <form id="finalForm" action="../../contorlador/C_juego.php" method="post" class="hidden-form">
        <input type="hidden" name="action" value="finalizar">
        <input type="hidden" name="login" value="<?= $nombreUsuario ?>">
        <input type="hidden" name="id_palabra" value="<?= $idPalabraSecreta ?>">
        <input type="hidden" name="letras_acertadas" id="f_acertadas" value="0">
        <input type="hidden" name="letras_falladas" id="f_falladas" value="0">
        <input type="hidden" name="palabra_acertada" id="f_acertada" value="0">
        <input type="hidden" name="puntuacion_obtenida" id="f_puntos" value="0">
    </form>
    <div id="game-config"
        data-secreta="<?= strtolower($palabraSecreta) ?>"
        data-maxfallos="<?= $maximoFallos ?>"
        data-login="<?= $nombreUsuario ?>"
        data-id-palabra="<?= $idPalabraSecreta ?>"></div>
    <script src="../../public/main.js"></script>
    </body>
</html>