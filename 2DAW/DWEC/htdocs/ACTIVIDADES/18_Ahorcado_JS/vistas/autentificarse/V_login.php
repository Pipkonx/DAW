<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../../public/style.css">
</head>

<body>
<?php
    $mensajeError = $_GET['error'] ?? '';
    $mensajeExito = $_GET['ok'] ?? '';
    $nombreUsuario = $_GET['login'] ?? '';

    if (!empty($mensajeError)) {
        echo '<div class="alert-error"><p>' . $mensajeError . '</p></div>';
    }
    if (!empty($mensajeExito)) {
        echo '<div class="alert-success"><p>' . $mensajeExito . '</p></div>';
    }
    ?>
<body>
    <h1>Login</h1>

    <div id="mensaje-login"></div>

    <form id="loginForm">
        <input type="hidden" name="action" value="login_json">
        <label for="login">Usuario</label>
        <input type="text" id="login" name="login" required value="<?= $nombreUsuario ?>">

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn-submit">Entrar</button>
    </form>

    <p>Necesitas cuenta? <a href="V_register.php">Registrarse</a></p>
</body>
<script>
    document.getElementById('loginForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        const mensajeDiv = document.getElementById('mensaje-login');

        fetch('../../contorlador/C_juego.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mensajeDiv.innerHTML = '<div class="alert-success"><p>' + data.message + '</p></div>';
                if (data.redirect) {
                    window.location = data.redirect;
                }
            } else {
                mensajeDiv.innerHTML = '<div class="alert-error"><p>' + data.message + '</p></div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mensajeDiv.innerHTML = '<div class="alert-error"><p>Ocurrió un error al intentar iniciar sesión.</p></div>';
        });
    });
</script>
</html>