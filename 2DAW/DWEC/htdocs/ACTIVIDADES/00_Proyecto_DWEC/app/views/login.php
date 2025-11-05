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
    <title>Inicio de sesión</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
    <header>
        <h1>Inicio de sesión</h1>
    </header>
    <main class="container">
        <form id="loginForm" class="form-card">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required placeholder="tu@correo.com">

            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required placeholder="••••••••">

            <button type="submit" class="btn">Entrar</button>
            <p class="alt-link">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
        </form>
    </main>

    <script type="module" src="../../public/js/main.js"></script>
</body>
</html>