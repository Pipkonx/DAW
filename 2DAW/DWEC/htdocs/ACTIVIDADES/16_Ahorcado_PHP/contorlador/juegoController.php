<?php

require_once __DIR__ . '/../conexion/DB.php';
require_once __DIR__ . '/utilsController.php';

$pdo = DB::getInstance()->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'categorias') {
    $declaracion = $pdo->query('SELECT id_categoria, nombre_categoria FROM CATEGORIAS ORDER BY nombre_categoria');
    $categorias = $declaracion->fetchAll();
    header('Content-Type: application/json');
    echo json_encode($categorias);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'start') {
    $nombreUsuario = trim($_POST['login'] ?? '');
    $idCategoria = isset($_POST['categoria']) ? intval($_POST['categoria']) : 0;
    $dificultadJuego = $_POST['dificultad'] ?? '';

    if ($nombreUsuario === '' || $idCategoria <= 0 || $dificultadJuego === '') {
        redirect('../vistas/juego/configurar.php?login=' . urlencode($nombreUsuario) . '&error=Datos+inv%C3%A1lidos');
    }

    $condicionLongitud = '';
    $maximoFallos = 6;

    switch ($dificultadJuego) {
        case 'facil':
            $condicionLongitud = 'CHAR_LENGTH(texto_palabra) <= 5';
            $maximoFallos = 8;
            break;
        case 'media':
            $condicionLongitud = 'CHAR_LENGTH(texto_palabra) BETWEEN 6 AND 8';
            $maximoFallos = 6;
            break;
        case 'dificil':
            $condicionLongitud = 'CHAR_LENGTH(texto_palabra) >= 9';
            $maximoFallos = 5;
            break;
        default:
            redirect('../vistas/juego/configurar.php?login=' . urlencode($nombreUsuario) . '&error=Dificultad+inv%C3%A1lida');
    }

    $sqlPalabra = "SELECT id_palabra, texto_palabra FROM PALABRAS WHERE id_categoria = ? AND " .
        $condicionLongitud . " ORDER BY RAND() LIMIT 1";
    $declaracion = $pdo->prepare($sqlPalabra);
    $declaracion->execute([$idCategoria]);
    $palabraSeleccionada = $declaracion->fetch();

    if (!$palabraSeleccionada) {
        redirect('../vistas/juego/configurar.php?login=' . urlencode($nombreUsuario) . '&error=No+hay+palabras+para+esa+categor%C3%ADa+dificultad');
    }

    $idPalabra = intval($palabraSeleccionada['id_palabra']);
    $textoPalabra = $palabraSeleccionada['texto_palabra'];

    $urlRedireccion = '../vistas/juego/partidaView.php?login=' . urlencode($nombreUsuario)
        . '&id_palabra=' . $idPalabra
        . '&palabra=' . urlencode($textoPalabra)
        . '&dificultad=' . urlencode($dificultadJuego)
        . '&maxfallos=' . $maximoFallos;
    redirect($urlRedireccion);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'finalizar') {
    $nombreUsuario = trim($_POST['login'] ?? '');
    $idPalabraJugada = intval($_POST['id_palabra'] ?? 0);
    $letrasAcertadas = intval($_POST['letras_acertadas'] ?? 0);
    $letrasFalladas = intval($_POST['letras_falladas'] ?? 0);
    $palabraAcertada = isset($_POST['palabra_acertada']) && ($_POST['palabra_acertada'] == '1' || $_POST['palabra_acertada'] === 'true') ? 1 : 0;
    $puntuacionObtenida = intval($_POST['puntuacion_obtenida'] ?? 0);

    if ($nombreUsuario === '' || $idPalabraJugada <= 0) {
        http_response_code(400);
        echo 'Datos inválidos';
        exit;
    }

    $declaracionUsuario = $pdo->prepare('SELECT id_jugador FROM JUGADORES WHERE login = ? LIMIT 1');
    $declaracionUsuario->execute([$nombreUsuario]);
    $filaJugador = $declaracionUsuario->fetch();

    if (!$filaJugador) {
        http_response_code(404);
        echo 'Jugador no encontrado';
        exit;
    }
    $idJugador = intval($filaJugador['id_jugador']);

    $insertarPartida = $pdo->prepare('INSERT INTO PARTIDAS (id_jugador, id_palabra_jugada, fecha_partida, letras_acertadas, letras_falladas, palabra_acertada, puntuacion_obtenida) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $fechaActual = date('Y-m-d H:i:s');
    $insertarPartida->execute([$idJugador, $idPalabraJugada, $fechaActual, $letrasAcertadas, $letrasFalladas, $palabraAcertada, $puntuacionObtenida]);

    redirect('../vistas/juego/configurarView.php?login=' . urlencode($nombreUsuario) . '&ok=Partida+guardada');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login_json') {
    header('Content-Type: application/json');
    $nombreUsuario = trim($_POST['login'] ?? '');
    $contrasena = $_POST['password'] ?? '';

    if (empty($nombreUsuario) || empty($contrasena)) {
        echo json_encode(['error' => 'Usuario o contraseña vacíos.']);
        exit;
    }

    $declaracion = $pdo->prepare('SELECT id_jugador, login, password FROM JUGADORES WHERE login = ? LIMIT 1');
    $declaracion->execute([$nombreUsuario]);
    $usuario = $declaracion->fetch();

    if ($usuario && password_verify($contrasena, $usuario['password'])) {
        echo json_encode(['ok' => true, 'login' => $usuario['login']]);
        exit;
    } else {
        echo json_encode(['error' => 'Credenciales inválidas.']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register_json') {
    header('Content-Type: application/json');
    $nombreUsuario = trim($_POST['login'] ?? '');
    $contrasena = $_POST['password'] ?? '';

    // Verifica si el nombre de usuario o la contraseña están vacíos.
    if (empty($nombreUsuario) || empty($contrasena)) {
        // Si están vacíos, envía un mensaje de error en formato JSON.
        echo json_encode(['error' => 'Usuario o contraseña vacíos.']);
        exit;
    }

    // Verifica si el nombre de usuario ya existe en la base de datos.
    $declaracion = $pdo->prepare('SELECT id_jugador FROM JUGADORES WHERE login = ? LIMIT 1');
    $declaracion->execute([$nombreUsuario]);
    if ($declaracion->fetch()) {
        // Si el usuario ya existe, envía un mensaje de error en formato JSON.
        echo json_encode(['error' => 'El nombre de usuario ya existe.']);
        exit;
    }

    // Hashea la contraseña para almacenarla de forma segura.
    $contrasenaHasheada = password_hash($contrasena, PASSWORD_DEFAULT);

    // Inserta el nuevo usuario en la base de datos.
    $declaracion = $pdo->prepare('INSERT INTO JUGADORES (login, password) VALUES (?, ?)');
    if ($declaracion->execute([$nombreUsuario, $contrasenaHasheada])) {
        // Si el registro es exitoso, envía un mensaje de éxito en formato JSON.
        echo json_encode(['ok' => true, 'message' => 'Registro completado.']);
        exit;
    } else {
        // Si ocurre un error al registrar, envía un mensaje de error en formato JSON.
        echo json_encode(['error' => 'Error al registrar el usuario.']);
        exit;
    }
}

// Si ninguna de las acciones anteriores se ejecutó, significa que la acción no es soportada.
// Envía un código de estado HTTP 400 y un mensaje de error.
http_response_code(400);
echo 'Acción no soportada';

?>