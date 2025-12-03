<!DOCTYPE html>
<html lang="es">

<?php
    $tituloPagina = "Panel de Administración";
    require_once __DIR__ . '/../comun/V_header.php';
    require_once __DIR__ . '/../../conexion/DB.php';
    require_once __DIR__ . '/../../modelos/M_auth.php';
    require_once __DIR__ . '/../../modelos/M_juego.php';

    session_start();

    // Verificar si el usuario está logueado y es administrador
    if (!isset($_SESSION['login']) || !isset($_SESSION['es_administrador']) || !$_SESSION['es_administrador']) {
        header('Location: ../autentificarse/V_login.php?error=Acceso denegado. Solo administradores.');
        exit();
    }

    $pdo = DB::getInstance()->getConnection();
    $modeloJuego = new M_juego();
    $mensajeError = '';
    $mensajeExito = '';

    // Lógica para añadir usuario
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_user') {
        $login = trim($_POST['login'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $es_administrador = isset($_POST['es_administrador']) ? 1 : 0;

        $result = manejarRegistroAdmin($pdo, $login, $password, $confirm_password, $es_administrador);
        if ($result['success']) {
            $mensajeExito = $result['message'];
        } else {
            $mensajeError = $result['message'];
        }
    }

    // Lógica para cambiar rol de usuario
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_role') {
        $id_jugador = $_POST['id_jugador'] ?? 0;
        $es_administrador = $_POST['es_administrador'] ?? 0;

        $stmt = $pdo->prepare("UPDATE JUGADORES SET es_administrador = ? WHERE id_jugador = ?");
        if ($stmt->execute([$es_administrador, $id_jugador])) {
            $mensajeExito = "Rol de usuario actualizado correctamente.";
        } else {
            $mensajeError = "Error al actualizar el rol del usuario.";
        }
    }

    // Lógica para añadir palabra
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_word') {
        $textoPalabra = trim($_POST['texto_palabra'] ?? '');
        $idCategoria = $_POST['id_categoria'] ?? 0;
        $result = $modeloJuego->añadirPalabra($textoPalabra, $idCategoria);
        if ($result['success']) {
            $mensajeExito = $result['message'];
        } else {
            $mensajeError = $result['message'];
        }
    }

    // Lógica para actualizar palabra
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_word') {
        $idPalabra = $_POST['id_palabra'] ?? 0;
        $textoPalabra = trim($_POST['texto_palabra'] ?? '');
        $idCategoria = $_POST['id_categoria'] ?? 0;
        $result = $modeloJuego->actualizarPalabra($idPalabra, $textoPalabra, $idCategoria);
        if ($result['success']) {
            $mensajeExito = $result['message'];
        } else {
            $mensajeError = $result['message'];
        }
    }

    // Lógica para eliminar palabra
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_word') {
        $idPalabra = $_POST['id_palabra'] ?? 0;
        $result = $modeloJuego->eliminarPalabra($idPalabra);
        if ($result['success']) {
            $mensajeExito = $result['message'];
        } else {
            $mensajeError = $result['message'];
        }
    }

    // Lógica para añadir categoría
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_category') {
        $nombreCategoria = trim($_POST['nombre_categoria'] ?? '');
        $result = $modeloJuego->añadirCategoria($nombreCategoria);
        if ($result['success']) {
            $mensajeExito = $result['message'];
        } else {
            $mensajeError = $result['message'];
        }
    }

    // Lógica para eliminar categoría
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_category') {
        $idCategoria = $_POST['id_categoria'] ?? 0;
        $result = $modeloJuego->eliminarCategoria($idCategoria);
        if ($result['success']) {
            $mensajeExito = $result['message'];
        } else {
            $mensajeError = $result['message'];
        }
    }

    // Obtener lista de usuarios
    $stmt = $pdo->query("SELECT id_jugador, login, es_administrador FROM JUGADORES");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener lista de palabras y categorías
    $palabras = $modeloJuego->obtenerPalabras();
    $categorias = $modeloJuego->obtenerCategorias();

?>

<body>
    <h1>Panel de Administración</h1>
    <div class="flex-container">
        <a href="../juego/V_configurar.php?login=<?= urlencode($_SESSION['login']) ?>" class="base-button">Volver a Configurar Juego</a>
        <p>Usuario: <?= $_SESSION['login'] ?></p>
        <a href="../../contorlador/C_juego.php?action=logout">Cerrar Sesión</a>
    </div>

    <?php if ($mensajeError): ?>
        <p class="error-message"><?= $mensajeError ?></p>
    <?php endif; ?>
    <?php if ($mensajeExito): ?>
        <p class="success-message"><?= $mensajeExito ?></p>
    <?php endif; ?>

    <h2>Añadir Nuevo Usuario</h2>
    <form action="" method="post">
        <input type="hidden" name="action" value="add_user">
        <label for="login">Login:</label>
        <input type="text" id="login" name="login" required>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <label for="confirm_password">Confirmar Contraseña:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <label for="es_administrador">Es Administrador:</label>
        <input type="checkbox" id="es_administrador" name="es_administrador">
        <button type="submit" class="btn-submit">Añadir Usuario</button>
    </form>

    <h2>Gestión de Usuarios</h2>
    <table id="tablaUsuarios">
        <thead>
            <tr>
                <th>ID</th>
                <th>Login</th>
                <th>Rol</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td><?= $usuario['id_jugador'] ?></td>
                    <td><?= $usuario['login'] ?></td>
                    <td><?= $usuario['es_administrador'] ? 'Administrador' : 'Jugador' ?></td>
                    <td>
                        <form action="" method="post" class="inline-form">
                            <input type="hidden" name="action" value="change_role">
                            <input type="hidden" name="id_jugador" value="<?= $usuario['id_jugador'] ?>">
                            <select name="es_administrador" onchange="this.form.submit()">
                                <option value="0" <?= !$usuario['es_administrador'] ? 'selected' : '' ?>>Jugador</option>
                                <option value="1" <?= $usuario['es_administrador'] ? 'selected' : '' ?>>Administrador</option>
                            </select>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Añadir Nueva Palabra</h2>
    <form action="" method="post">
        <input type="hidden" name="action" value="add_word">
        <label for="texto_palabra">Palabra:</label>
        <input type="text" id="texto_palabra" name="texto_palabra" required>
        <label for="id_categoria_palabra">Categoría:</label>
        <select id="id_categoria_palabra" name="id_categoria" required>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= $categoria['id_categoria'] ?>"><?= $categoria['nombre_categoria'] ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-submit">Añadir Palabra</button>
    </form>

    <h2>Gestión de Palabras</h2>
    <table id="tablaPalabras">
        <thead>
            <tr>
                <th>ID</th>
                <th>Palabra</th>
                <th>Categoría</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($palabras as $palabra): ?>
                <tr>
                    <td><?= $palabra['id_palabra'] ?></td>
                    <td><?= $palabra['texto_palabra'] ?></td>
                    <td><?= $palabra['nombre_categoria'] ?></td>
                    <td>
                        <input type="text" id="texto_palabra_<?= $palabra['id_palabra'] ?>" value="<?= $palabra['texto_palabra'] ?>" required>
                        <select id="id_categoria_palabra_<?= $palabra['id_palabra'] ?>">
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id_categoria'] ?>" <?= ($categoria['id_categoria'] == $palabra['id_categoria']) ? 'selected' : '' ?>><?= $categoria['nombre_categoria'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" title="Actualizar" onclick="updateWord(<?= $palabra['id_palabra'] ?>, document.getElementById('texto_palabra_<?= $palabra['id_palabra'] ?>').value, document.getElementById('id_categoria_palabra_<?= $palabra['id_palabra'] ?>').value)">✏️</button>
                        <button type="button" title="Eliminar" onclick="deleteWord(<?= $palabra['id_palabra'] ?>)">🗑️</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Añadir Nueva Categoría</h2>
    <form action="" method="post">
        <input type="hidden" name="action" value="add_category">
        <label for="nombre_categoria">Nombre de la Categoría:</label>
        <input type="text" id="nombre_categoria" name="nombre_categoria" required>
        <button type="submit" class="btn-submit">Añadir Categoría</button>
    </form>

    <h2>Gestión de Categorías</h2>
    <table id="tablaCategorias">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categorias as $categoria): ?>
                <tr>
                    <td><?= $categoria['id_categoria'] ?></td>
                    <td><?= $categoria['nombre_categoria'] ?></td>
                    <td>
                        <button type="button" title="Eliminar" onclick="deleteCategory(<?= $categoria['id_categoria'] ?>)">🗑️</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>



    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script>
        function updateWord(idPalabra, newTextoPalabra, newIdCategoria) {
            if (confirm('¿Estás seguro de que quieres actualizar esta palabra?')) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'update_word',
                        id_palabra: idPalabra,
                        texto_palabra: newTextoPalabra,
                        id_categoria: newIdCategoria
                    })
                }).then(response => response.text())
                  .then(data => {
                      location.reload();
                  }).catch(error => {
                      console.error('Error:', error);
                      alert('Error al actualizar la palabra.');
                  });
            }
        }

        function deleteWord(idPalabra) {
            if (confirm('¿Estás seguro de que quieres eliminar esta palabra?')) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'delete_word',
                        id_palabra: idPalabra
                    })
                }).then(response => response.text())
                  .then(data => {
                      location.reload();
                  }).catch(error => {
                      console.error('Error:', error);
                      alert('Error al eliminar la palabra.');
                  });
            }
        }

        function deleteCategory(idCategoria) {
            if (confirm('¿Estás seguro de que quieres eliminar esta categoría? Esto eliminará todas las palabras asociadas.')) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'delete_category',
                        id_categoria: idCategoria
                    })
                }).then(response => response.text())
                  .then(data => {
                      location.reload();
                  }).catch(error => {
                      console.error('Error:', error);
                      alert('Error al eliminar la categoría.');
                  });
            }
        }

        $(document).ready(function() {
            $('#tablaUsuarios').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                }
            });
            $('#tablaPalabras').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                }
            });
            $('#tablaCategorias').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                }
            });
        });
    </script>
</body>

</html>