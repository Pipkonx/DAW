<?php
require_once __DIR__ . '/../../conexion/DB.php';

$nombreUsuario = isset($_GET['login']) ? trim($_GET['login']) : '';
$pdo = DB::getInstance()->getConnection();

$mensajeError = '';
$listaPartidas = [];
$datosUsuario = null;

if ($nombreUsuario === '') {
    $mensajeError = 'Falta el usuario (login).';
} else {
    $declaracionUsuario = $pdo->prepare('SELECT id_jugador, login FROM JUGADORES WHERE login = ? LIMIT 1');
    $declaracionUsuario->execute([$nombreUsuario]);
    $datosUsuario = $declaracionUsuario->fetch();

    if (!$datosUsuario) {
        $mensajeError = 'Usuario no encontrado.';
    } else {
        $consultaPartidas = $pdo->prepare('SELECT p.id_partida, p.fecha_partida, p.letras_acertadas, p.letras_falladas, p.palabra_acertada, p.puntuacion_obtenida, w.texto_palabra
                            FROM PARTIDAS p
                            JOIN PALABRAS w ON w.id_palabra = p.id_palabra_jugada
                            WHERE p.id_jugador = ?
                            ORDER BY p.fecha_partida DESC');
        $consultaPartidas->execute([$datosUsuario['id_jugador']]);
        $listaPartidas = $consultaPartidas->fetchAll();
    }
}

$tituloPagina = "Mis Partidas";
require_once __DIR__ . '/../comun/V_header.php';
?>

<body>
    <h1>Mis Partidas</h1>
    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
        <a href="V_configurar.php?login=<?= urlencode($nombreUsuario) ?>">Volver a Configurar Juego</a>
        <a href="../../contorlador/C_juego.php?action=logout">Cerrar Sesión</a>
    </div>
    <div class="meta">
        Usuario: <?= $nombreUsuario ?>
    </div>

    <?php if ($mensajeError): ?>
        <p class="empty"><?= $mensajeError ?></p>
    <?php else: ?>
        <?php if (empty($listaPartidas)): ?>
            <p class="empty">Aún no tienes partidas registradas.</p>
        <?php else: ?>
            <table>
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

</body>

</html>