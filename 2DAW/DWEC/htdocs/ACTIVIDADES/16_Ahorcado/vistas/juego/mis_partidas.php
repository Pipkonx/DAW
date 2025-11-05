<?php
require_once __DIR__ . '/../../conexion/config.php';

$login = isset($_GET['login']) ? trim($_GET['login']) : '';
$pdo = Database::getInstance()->getConnection();

$error = '';
$partidas = [];
$usuario = null;

if ($login === '') {
    $error = 'Falta el usuario (login).';
} else {
    // Obtener usuario
    $stmt = $pdo->prepare('SELECT id_jugador, login FROM JUGADORES WHERE login = ? LIMIT 1');
    $stmt->execute([$login]);
    $usuario = $stmt->fetch();
    if (!$usuario) {
        $error = 'Usuario no encontrado.';
    } else {
        // Cargar partidas del usuario
        $q = $pdo->prepare('SELECT p.id_partida, p.fecha_partida, p.letras_acertadas, p.letras_falladas, p.palabra_acertada, p.puntuacion_obtenida, w.texto_palabra
                            FROM PARTIDAS p
                            JOIN PALABRAS w ON w.id_palabra = p.id_palabra_jugada
                            WHERE p.id_jugador = ?
                            ORDER BY p.fecha_partida DESC');
        $q->execute([intval($usuario['id_jugador'])]);
        $partidas = $q->fetchAll();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Partidas</title>
</head>
<body>
    <h1>Mis Partidas</h1>
    <div class="meta">
        Usuario: <?= htmlspecialchars($login) ?>
    </div>

    <?php if ($error): ?>
        <p class="empty"><?= htmlspecialchars($error) ?></p>
    <?php else: ?>
        <?php if (empty($partidas)): ?>
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
                    <?php foreach ($partidas as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['fecha_partida']) ?></td>
                            <td><?= htmlspecialchars($p['texto_palabra']) ?></td>
                            <td><?= intval($p['letras_acertadas']) ?></td>
                            <td><?= intval($p['letras_falladas']) ?></td>
                            <td><?= intval($p['palabra_acertada']) ? 'Sí' : 'No' ?></td>
                            <td><?= intval($p['puntuacion_obtenida']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endif; ?>

    <div class="actions">
        <a href="configurar.php?login=<?= urlencode($login) ?>">Volver a Configurar Juego</a>
    </div>
</body>
</html>