<?php
    $tituloPagina = "Login";
    require_once __DIR__ . '/../comun/V_header.php';
    $mensajeError = isset($_GET['error']) ? $_GET['error'] : '';
    $mensajeExito = isset($_GET['ok']) ? $_GET['ok'] : '';
    $nombreUsuario = isset($_GET['login']) ? $_GET['login'] : '';
    ?>
<body>
    <h1>Login</h1>

    <?php if ($mensajeError): ?>
        <div class="alert-error"><p><?= $mensajeError ?></p></div>
    <?php endif; ?>
    <?php if ($mensajeExito): ?>
        <div class="alert-success"><p><?= $mensajeExito ?><?= $nombreUsuario ? ' — Usuario: ' . $nombreUsuario : '' ?></p></div>
    <?php endif; ?>

    <form action="../../contorlador/C_juego.php" method="post">
        <input type="hidden" name="action" value="login_json">
        <label for="login">Usuario</label>
        <input type="text" id="login" name="login" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn-submit">Entrar</button>
    </form>

    <p>Necesitas cuenta? <a href="V_register.php">Registrarse</a></p>
</body>

</html>