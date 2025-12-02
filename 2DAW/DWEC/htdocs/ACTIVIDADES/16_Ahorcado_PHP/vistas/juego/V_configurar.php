<!DOCTYPE html>
<html lang="es">

<?php
    $tituloPagina = "Configurar Juego";
    require_once __DIR__ . '/../comun/V_header.php';
    $nombreUsuario = isset($_GET['login']) ? $_GET['login'] : '';
    $mensajeError = isset($_GET['error']) ? $_GET['error'] : '';
    $mensajeExito = isset($_GET['ok']) ? $_GET['ok'] : '';
?>

<body>
    <h1>Configurar Juego</h1>
    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
        <a href="V_misPartidas.php?login=<?= urlencode($nombreUsuario) ?>">Ver mis partidas</a>
        <a href="../../contorlador/C_juego.php?action=logout">Cerrar Sesión</a>
    </div>
    <?php if ($mensajeError): ?>
        <p><?= $mensajeError ?></p>
    <?php endif; ?>
    <?php if ($mensajeExito): ?>
        <p><?= $mensajeExito ?></p>
    <?php endif; ?>

    <p>Usuario: <?= $nombreUsuario ? $nombreUsuario : 'Invitado' ?></p>

    <form action="../../contorlador/C_juego.php" method="post">
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
</body>

</html>