<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../../public/style.css">
</head>

<body>
<?php
    $mensajeError = $_GET['error'] ?? '';
    if (!empty($mensajeError)) {
        echo '<div class="alert-error"><p>' . $mensajeError . '</p></div>';
    }
    ?>
<body>
    <h1>Registrarse</h1>

    <div id="mensaje-registro"></div>

    <form id="registerForm">
        <input type="hidden" name="action" value="register_json">

        <label for="login">Usuario</label>
        <input type="text" id="login" name="login" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm">Confirmar contraseña</label>
        <input type="password" id="confirm" name="confirm" required>

        <button type="submit" class="btn-submit">Crear cuenta</button>
    </form>

    <p>Ya estás registrado? <a href="V_login.php">Iniciar sesión</a></p>
    <p>
        <a href="../../contorlador/C_juego.php?action=logout">Cerrar Sesión</a>
    </p>
</body>
<script>
    document.getElementById('registerForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        const mensajeDiv = document.getElementById('mensaje-registro');

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
            mensajeDiv.innerHTML = '<div class="alert-error"><p>Ocurrió un error al intentar registrar el usuario.</p></div>';
        });
    });
</script>
</html>