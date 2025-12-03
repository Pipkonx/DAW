<!DOCTYPE html>
<html lang="es">

<?php
session_start();
$tituloPagina = "Configurar Juego";
require_once __DIR__ . '/../comun/V_header.php';
$nombreUsuario = isset($_GET['login']) ? $_GET['login'] : '';
$mensajeError = isset($_GET['error']) ? $_GET['error'] : '';
$mensajeExito = isset($_GET['ok']) ? $_GET['ok'] : '';

require_once __DIR__ . '/../../conexion/DB.php';
require_once __DIR__ . '/../../modelos/M_juego.php';
$pdo = DB::getInstance()->getConnection();

$modeloJuego = new M_juego();
$categorias = $modeloJuego->obtenerCategorias();

$listaPartidas = [];
$datosUsuario = null;

if ($nombreUsuario === '') {
    // Si el nombre de usuario está vacío, se añade un mensaje de error si no hay uno ya.
    if (empty($mensajeError)) {
        $mensajeError = 'Falta el usuario (login).';
    }
} else {
    $declaracionUsuario = $pdo->prepare('SELECT id_jugador, login, es_administrador FROM JUGADORES WHERE login = ? LIMIT 1');
    $declaracionUsuario->execute([$nombreUsuario]);
    $datosUsuario = $declaracionUsuario->fetch();

    if (!$datosUsuario) {
        // Si el usuario no se encuentra, se añade un mensaje de error si no hay uno ya.
        if (empty($mensajeError)) {
            $mensajeError = 'Usuario no encontrado.';
        }
    } else {
        $_SESSION['login'] = $datosUsuario['login'];
        $_SESSION['es_administrador'] = $datosUsuario['es_administrador'];

        $consultaPartidas = $pdo->prepare('SELECT p.id_partida, p.fecha_partida, p.letras_acertadas, p.letras_falladas, p.palabra_acertada, p.puntuacion_obtenida, w.texto_palabra
                                FROM PARTIDAS p
                                JOIN PALABRAS w ON w.id_palabra = p.id_palabra_jugada
                                WHERE p.id_jugador = ?
                                ORDER BY p.fecha_partida DESC');
        $consultaPartidas->execute([$datosUsuario['id_jugador']]);
        $listaPartidas = $consultaPartidas->fetchAll();
    }
}
?>

<body>
    <h1>Configurar Juego</h1>
    <div class="flex-container">
        <p>Usuario: <?= $nombreUsuario ?></p>
        <a href="../../contorlador/C_juego.php?action=logout">Cerrar Sesión</a>
        <?php if (isset($_SESSION['es_administrador']) && $_SESSION['es_administrador']): ?>
            <a href="../admin/V_admin.php">Panel de Administración</a>
        <?php endif; ?>
    </div>
    <?php if ($mensajeError): ?>
        <p><?= $mensajeError ?></p>
    <?php endif; ?>
    <?php if ($mensajeExito): ?>
        <p><?= $mensajeExito ?></p>
    <?php endif; ?>

    <p>Usuario: <?= $nombreUsuario ? $nombreUsuario : 'Invitado' ?></p>

        <input type="hidden" name="action" value="start">
        <input type="hidden" name="login" value="<?= $nombreUsuario ?>">

        <label for="categoria">Categoría</label>
        <select id="categoria" name="categoria" required>
            <option value="">Selecciona una categoria</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= $categoria['id_categoria'] ?>"><?= $categoria['nombre_categoria'] ?></option>
            <?php endforeach; ?>
        </select>

        <label for="dificultad">Dificultad</label>
        <select id="dificultad" name="dificultad" required>
            <option value="">Selecciona una dificultad</option>
            <option value="facil">Fácil</option>
            <option value="media">Media</option>
            <option value="dificil">Difícil</option>
        </select>

        <button type="submit" class="btn-submit">Comenzar</button>

    <h2>Mis Partidas</h2>


    <?php if ($mensajeError): ?>
        <p class="empty"><?= $mensajeError ?></p>
    <?php else: ?>
        <?php if (empty($listaPartidas)): ?>
            <p class="empty">Aún no tienes partidas registradas.</p>
        <?php else: ?>
            <table id="tablaPartidas">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Palabra</th>
                        <th>Acertadas</th>
                        <th>Falladas</th>
                        <th>¿Acertada?</th>
                        <th>Puntuación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($listaPartidas as $partida): ?>
                        <tr>
                            <td><?= $partida['fecha_partida'] ?></td>
                            <td><?= $partida['texto_palabra'] ?></td>
                            <td><?= $partida['letras_acertadas'] ?></td>
                            <td><?= $partida['letras_falladas'] ?></td>
                            <td><?= $partida['palabra_acertada'] ? 'Sí' : 'No' ?></td>
                            <td><?= $partida['puntuacion_obtenida'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>

    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script>
        $(document).ready(function() {
            $('#tablaPartidas').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"
                }
            });
        });
    </script>
    <script src="../public/main.js"></script>
</body>
</html>