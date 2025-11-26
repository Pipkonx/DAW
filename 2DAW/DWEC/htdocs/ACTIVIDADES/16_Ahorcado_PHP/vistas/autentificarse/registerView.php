<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <?php
    $mensajeError = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
    ?>
</head>

<body>
    <h1>Registrarse</h1>

    <?php if ($mensajeError): ?>
        <p><?= $mensajeError ?></p>
    <?php endif; ?>

    <form action="../../contorlador/authController.php" method="post">
        <input type="hidden" name="action" value="register">

        <label for="login">Usuario</label>
        <input type="text" id="login" name="login" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm">Confirmar contraseña</label>
        <input type="password" id="confirm" name="confirm" required>

        <button type="submit">Crear cuenta</button>
    </form>

    <p>Ya estás registrado? <a href="loginView.php">Iniciar sesión</a></p>
</body>

</html>