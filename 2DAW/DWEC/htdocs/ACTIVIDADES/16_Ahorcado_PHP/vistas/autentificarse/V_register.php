<?php
    $tituloPagina = "Registro";
    require_once __DIR__ . '/../comun/V_header.php';
    $mensajeError = isset($_GET['error']) ? $_GET['error'] : '';
    ?>
<body>
    <h1>Registrarse</h1>

    <?php if ($mensajeError): ?>
        <p><?= $mensajeError ?></p>
    <?php endif; ?>

    <form action="../../contorlador/C_juego.php" method="post">
        <input type="hidden" name="action" value="register_json">

        <label for="login">Usuario</label>
        <input type="text" id="login" name="login" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm">Confirmar contraseña</label>
        <input type="password" id="confirm" name="confirm" required>

        <button type="submit">Crear cuenta</button>
    </form>

    <p>Ya estás registrado? <a href="V_login.php">Iniciar sesión</a></p>
    <p>
        <a href="../../contorlador/C_juego.php?action=logout">Cerrar Sesión</a>
    </p>
</body>

</html>