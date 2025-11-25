<?php
require_once __DIR__ . '/../conexion/DB.php';
require_once __DIR__ . '/utils.php';

$pdo = Database::getInstance()->getConnection();

// Obtener categorías
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'categorias') {
    $stmt = $pdo->query('SELECT id_categoria, nombre_categoria FROM CATEGORIAS ORDER BY nombre_categoria');
    $cats = $stmt->fetchAll();
    header('Content-Type: application/json');
    // json_encode convierte el array en una cadena json
    echo json_encode($cats);
    exit;
}

// para iniciar la partida seleccionar la dificultad y la categoria de la palabra
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'start') {
    $login = trim($_POST['login'] ?? '');
    $categoria = isset($_POST['categoria']) ? intval($_POST['categoria']) : 0;
    $dificultad = $_POST['dificultad'] ?? '';

    if ($login === '' || $categoria <= 0 || $dificultad === '') {
        redirect('../vistas/juego/configurar.php?login=' . urlencode($login) . '&error=Datos+inv%C3%A1lidos');
    }

    // dificultad y maximo de fallos
    $lenCond = '';
    $maxFallos = 6;
    switch ($dificultad) {
        case 'facil':
            $lenCond = 'CHAR_LENGTH(texto_palabra) <= 5';
            $maxFallos = 8;
            break;
        case 'media':
            $lenCond = 'CHAR_LENGTH(texto_palabra) BETWEEN 6 AND 8';
            $maxFallos = 6;
            break;
        case 'dificil':
            $lenCond = 'CHAR_LENGTH(texto_palabra) >= 9';
            $maxFallos = 5;
            break;
        default:
            redirect('../vistas/juego/configurar.php?login=' . urlencode($login) . '&error=Dificultad+inv%C3%A1lida');
    }

    // Seleccionar palabra aleatoria que cumpla categoría y dificultad
    $sql = "SELECT id_palabra, texto_palabra FROM PALABRAS WHERE id_categoria = ? AND 
    -- rand es para seleccionar una palabra aleatoria de la categoria y dificultad
    $lenCond ORDER BY RAND() LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$categoria]);
    $pal = $stmt->fetch();
    if (!$pal) {
        redirect('../vistas/juego/configurar.php?login=' . urlencode($login) . '&error=No+hay+palabras+para+esa+categor%C3%ADa+dificultad');
    }
    // el intval convertimos el id de la palabra a entero
    $idPalabra = intval($pal['id_palabra']);
    $texto = $pal['texto_palabra'];

    // Redirigir a la vista de partida con parámetros necesarios
    $url = '../vistas/juego/partida.php?login=' . urlencode($login)
        . '&id_palabra=' . $idPalabra
        . '&palabra=' . urlencode($texto)
        . '&dificultad=' . urlencode($dificultad)
        . '&maxfallos=' . $maxFallos;
    redirect($url);
}

// Finalizar partida y guardar en BD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'finalizar') {
    $login = trim($_POST['login'] ?? '');
    $idPalabra = intval($_POST['id_palabra'] ?? 0);
    $letrasAcertadas = intval($_POST['letras_acertadas'] ?? 0);
    $letrasFalladas = intval($_POST['letras_falladas'] ?? 0);
    $palabraAcertada = isset($_POST['palabra_acertada']) && ($_POST['palabra_acertada'] == '1' || $_POST['palabra_acertada'] === 'true') ? 1 : 0;
    $puntuacion = intval($_POST['puntuacion_obtenida'] ?? 0);

    if ($login === '' || $idPalabra <= 0) {
        // http_response_code es para devolver el codigo de estado http
        http_response_code(400);
        echo 'Datos inválidos';
        exit;
    }

    // Obtener id_jugador
    $u = $pdo->prepare('SELECT id_jugador FROM JUGADORES WHERE login = ? LIMIT 1');
    $u->execute([$login]);
    $row = $u->fetch();
    if (!$row) {
        http_response_code(404);
        echo 'Jugador no encontrado';
        exit;
    }
    $idJugador = intval($row['id_jugador']);

    // Insertar partida
    $ins = $pdo->prepare('INSERT INTO PARTIDAS (id_jugador, id_palabra_jugada, fecha_partida, letras_acertadas, letras_falladas, palabra_acertada, puntuacion_obtenida) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $fecha = date('Y-m-d H:i:s');
    $ins->execute([$idJugador, $idPalabra, $fecha, $letrasAcertadas, $letrasFalladas, $palabraAcertada, $puntuacion]);

    // Volver a configuración con mensaje
    redirect('../vistas/juego/configurar.php?login=' . urlencode($login) . '&ok=Partida+guardada');
}

http_response_code(400);
echo 'Acción no soportada';
