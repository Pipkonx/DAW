<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partida</title>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../../public/style.css">
</head>

<body>
    <?php
    $error_message = '';

    $nombreUsuario = $_GET['login'] ?? '';
    $palabraSecreta = $_GET['palabra'] ?? '';
    $idPalabraSecreta = $_GET['id_palabra'] ?? '';
    $maximoFallos = isset($_GET['maxfallos']) ? $_GET['maxfallos'] : 6;

    // Asegurarse de que el usuario esté logueado (simplified check without sessions)
    if (empty($nombreUsuario)) {
        $error_message = 'Debes iniciar sesión para jugar.';
    } elseif (empty($palabraSecreta) || empty($idPalabraSecreta)) {
        $error_message = 'No se pudo iniciar la partida. Inténtalo de nuevo.';
    }

    if (!empty($error_message)) {
        echo '<p style="color: red;">' . $error_message . '</p>';
        // Optionally, you could include a link back to V_login or V_configurar here
        echo '<p><a href="../autentificarse/V_login.php">Volver al inicio de sesión</a></p>';
        echo '<p><a href="V_configurar.php?login=' . $nombreUsuario . '">Volver a Configurar Juego</a></p>';
        exit(); // Stop execution if there's an error
    }
    ?>

    <h1>Partida de Ahorcado</h1>
    <div class="flex-container">
        <a href="V_configurar.php?login=<?= $nombreUsuario ?>">Volver a Configurar Juego</a>
        <a href="#" id="logoutLink">Cerrar Sesión</a>
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

    <div id="game-config"
        data-secreta="<?= strtolower($palabraSecreta) ?>"
        data-maxfallos="<?= $maximoFallos ?>"
        data-login="<?= $nombreUsuario ?>"
        data-id-palabra="<?= $idPalabraSecreta ?>"></div>
    <script src="../../public/main.js"></script>
</body>

</html>