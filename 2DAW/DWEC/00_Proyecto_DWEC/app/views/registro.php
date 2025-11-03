<?php
require_once __DIR__ . '/../config/database.php';
ensure_session();
$csrf = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
    <header>
        <h1>Registro</h1>
    </header>
    <main class="container">
        <form id="registerForm" class="form-card">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" name="nombre" required placeholder="Tu nombre">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="tu@correo.com">

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required placeholder="••••••••" minlength="6">

            <label for="confirmar">Confirmar contraseña</label>
            <input type="password" id="confirmar" name="confirmar" required placeholder="••••••••" minlength="6">

            <button type="submit" class="btn">Crear cuenta</button>
            <p class="alt-link">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
        </form>
    </main>

    <script type="module" src="../../public/js/main.js"></script>
</body>
</html>