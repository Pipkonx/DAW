<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <?php
    $mensajeError = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
    $mensajeExito = isset($_GET['ok']) ? htmlspecialchars($_GET['ok']) : '';
    $nombreUsuario = isset($_GET['login']) ? htmlspecialchars($_GET['login']) : '';
    ?>
</head>

<body>
    <h1>Login</h1>

    <?php if ($mensajeError): ?>
        <p><?= $mensajeError ?></p>
    <?php endif; ?>
    <?php if ($mensajeExito): ?>
        <p><?= $mensajeExito ?><?= $nombreUsuario ? ' — Usuario: ' . $nombreUsuario : '' ?></p>
    <?php endif; ?>

    <form action="../../contorlador/authController.php" method="post">
        <input type="hidden" name="action" value="login">
        <label for="login">Usuario</label>
        <input type="text" id="login" name="login" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Entrar</button>
    </form>

    <p>Necesitas cuenta? <a href="registerView.php">Registrarse</a></p>
</body>

</html>