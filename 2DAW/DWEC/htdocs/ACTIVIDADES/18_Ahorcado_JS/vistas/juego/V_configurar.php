<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Juego</title>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="../../public/style.css">
</head>

<body>
<?php
$error_message = '';

$nombreUsuario = $_GET['login'] ?? '';

// Asegurarse de que el usuario esté logueado (simplified check without sessions)
if (empty($nombreUsuario)) {
    $error_message = 'Debes iniciar sesión para configurar el juego.';
}

if (!empty($error_message)) {
    echo '<p style="color: red;">' . $error_message . '</p>';
    echo '<p><a href="../autentificarse/V_login.php">Volver al inicio de sesión</a></p>';
    exit();
}
?>
    <h1>Configurar Juego</h1>
    <div class="flex-container">
        <p>Usuario: <?= $nombreUsuario ?></p>
        <a href="#" id="logoutLink">Cerrar Sesión</a>
    </div>
    <div id="mensaje-configurar"></div>

    <form id="startGameForm">
        <input type="hidden" name="action" value="start">
        <input type="hidden" name="login" value="<?= $nombreUsuario ?>">

    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            const mensajeDiv = document.getElementById('mensaje-configurar');
            const selectCategoria = document.getElementById('categoria');
            const tablaPartidas = $('#tablaPartidas').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                },
                "ajax": {
                    "url": "../../contorlador/C_juego.php?action=get_user_games&login=<?= $nombreUsuario ?>",
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "fecha_partida" },
                    { "data": "texto_palabra" },
                    { "data": "letras_acertadas" },
                    { "data": "letras_falladas" },
                    { "data": "palabra_acertada", "render": function(data) { return data ? 'Sí' : 'No'; } },
                    { "data": "puntuacion_obtenida" }
                ]
            });

            // Cargar categorías
            fetch('../../contorlador/C_juego.php?action=categorias')
                .then(response => response.json())
                .then(categorias => {
                    categorias.forEach(categoria => {
                        const option = document.createElement('option');
                        option.value = categoria.id_categoria;
                        option.textContent = categoria.nombre_categoria;
                        selectCategoria.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error al cargar categorías:', error);
                    mensajeDiv.innerHTML = '<div class="alert-error"><p>Error al cargar categorías.</p></div>';
                });

            // Manejar inicio de juego
            document.getElementById('startGameForm').addEventListener('submit', function(event) {
                event.preventDefault();

                const formData = new FormData(this);

                fetch('../../contorlador/C_juego.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    } else {
                        mensajeDiv.innerHTML = '<div class="alert-error"><p>' + data.message + '</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mensajeDiv.innerHTML = '<div class="alert-error"><p>Ocurrió un error al iniciar el juego.</p></div>';
                });
            });

            // Manejar cierre de sesión
            document.getElementById('logoutLink').addEventListener('click', function(event) {
                event.preventDefault();

                fetch('../../contorlador/C_juego.php?action=logout')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            }
                        } else {
                            mensajeDiv.innerHTML = '<div class="alert-error"><p>' + data.message + '</p></div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        mensajeDiv.innerHTML = '<div class="alert-error"><p>Ocurrió un error al cerrar sesión.</p></div>';
                    });
            });
        });
    </script>
</body>
</html>