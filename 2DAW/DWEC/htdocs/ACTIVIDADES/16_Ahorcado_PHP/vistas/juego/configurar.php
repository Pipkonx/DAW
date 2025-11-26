<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Juego</title>
    <?php
    $nombreUsuario = isset($_GET['login']) ? htmlspecialchars($_GET['login']) : '';
    $mensajeError = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
    $mensajeExito = isset($_GET['ok']) ? htmlspecialchars($_GET['ok']) : '';
    ?>
</head>

<body>
    <h1>Configurar Juego</h1>
    <?php if ($mensajeError): ?>
        <p><?= $mensajeError ?></p>
    <?php endif; ?>
    <?php if ($mensajeExito): ?>
        <p><?= $mensajeExito ?></p>
    <?php endif; ?>

    <p>Usuario: <?= $nombreUsuario ? $nombreUsuario : 'Invitado' ?></p>

    <form action="../../contorlador/juego.php" method="post">
        <input type="hidden" name="action" value="start">
        <input type="hidden" name="login" value="<?= $nombreUsuario ?>">

        <label for="categoria">Categoría</label>
        <select id="categoria" name="categoria" required>
            <option value="">Seleccionar Categoria</option>
            <option value="1">Animales</option>
            <option value="2">Colores</option>
            <option value="3">Frutas</option>
            <option value="4">Países</option>
        </select>

        <label for="dificultad">Dificultad</label>
        <select id="dificultad" name="dificultad" required>
            <option value="facil">Fácil</option>
            <option value="media">Media</option>
            <option value="dificil">Difícil</option>
        </select>

        <button type="submit">Comenzar</button>
    </form>

    <script src="../public/main.js"></script>
    <p>
        <a href="mis_partidas.php?login=<?= urlencode($nombreUsuario) ?>">Ver mis partidas</a>
    </p>
</body>

</html>