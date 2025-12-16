<?php
require_once __DIR__ . '/../conexion/DB.php';
require_once __DIR__ . '/C_utils.php';
require_once __DIR__ . '/../modelos/M_auth.php';
require_once __DIR__ . '/../modelos/M_juego.php';

$pdo = DB::getInstance()->getConnection();

if ($_GET && isset($_GET['action'])) {
    $accionGet = $_GET['action'];
    switch ($accionGet) {
        case 'categorias':
            $modeloJuego = new M_juego();
            $categorias = $modeloJuego->obtenerCategorias();
            header('Content-Type: application/json');
            echo json_encode($categorias);
            exit;
        case 'get_user_games':
            header('Content-Type: application/json');
            $nombreUsuario = $_GET['login'] ?? '';
            if (empty($nombreUsuario)) {
                echo json_encode([]);
                exit;
            }
            $declaracionUsuario = $pdo->prepare('SELECT id_jugador FROM JUGADORES WHERE login = ? LIMIT 1');
            $declaracionUsuario->execute([$nombreUsuario]);
            $datosUsuario = $declaracionUsuario->fetch();

            if (!$datosUsuario) {
                echo json_encode([]);
                exit;
            }
            $idJugador = $datosUsuario['id_jugador'];

            $consultaPartidas = $pdo->prepare('SELECT p.id_partida, p.fecha_partida, p.letras_acertadas, p.letras_falladas, p.palabra_acertada, p.puntuacion_obtenida, w.texto_palabra
                                FROM PARTIDAS p
                                JOIN PALABRAS w ON w.id_palabra = p.id_palabra_jugada
                                WHERE p.id_jugador = ?
                                ORDER BY p.fecha_partida DESC');
            $consultaPartidas->execute([$idJugador]);
            $listaPartidas = $consultaPartidas->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($listaPartidas);
            exit;
        case 'logout':
            echo json_encode(['success' => true, 'message' => 'Sesión cerrada correctamente.', 'redirect' => '../vistas/autentificarse/V_login.php']);
            break;
    }
}

if ($_POST) {
    if (isset($_POST['action'])) {
        $accion = $_POST['action'];
        switch ($accion) {
            case 'login_json':
                header('Content-Type: application/json');
                $nombreUsuario = trim($_POST['login'] ?? '');
                $contrasena = $_POST['password'] ?? '';

                if (empty($nombreUsuario) || empty($contrasena)) {
                    echo json_encode(['error' => 'Usuario o contraseña vacíos.']);
                    exit;
                }

                // Call the authentication function from M_auth.php
                $result = manejarInicioSesion($pdo, $nombreUsuario, $contrasena);
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => $result['message'], 'redirect' => '../vistas/juego/V_configurar.php']);
                } else {
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
                break;

            case 'register_json':
                header('Content-Type: application/json');
                $nombreUsuario = trim($_POST['login'] ?? '');
                $contrasena = $_POST['password'] ?? '';
                $confirmarContrasena = $_POST['confirm'] ?? '';

                if (empty($nombreUsuario) || empty($contrasena) || empty($confirmarContrasena)) {
                    echo json_encode(['error' => 'Completa todos los campos.']);
                    exit;
                }

                // Call the registration function from M_auth.php
                $result = manejarRegistro($pdo, $nombreUsuario, $contrasena, $confirmarContrasena);
                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => $result['message'], 'redirect' => '../vistas/autentificarse/V_login.php']);
                } else {
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
                break;
            case 'start':
                $nombreUsuario = trim($_POST['login'] ?? '');
                $idCategoria = $_POST['categoria'] ?? 0;
                $dificultadJuego = $_POST['dificultad'] ?? '';

                $modeloJuego = new M_juego();
                $result = $modeloJuego->iniciarPartida($nombreUsuario, $idCategoria, $dificultadJuego);

                if ($result['success']) {
                    echo json_encode(['success' => true, 'redirect' => $result['urlRedireccion']]);
                } else {
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
                break;

            case 'finalizar':
                $nombreUsuario = trim($_POST['login'] ?? '');
                $idPalabraJugada = $_POST['id_palabra'] ?? 0;
                $letrasAcertadas = $_POST['letras_acertadas'] ?? 0;
                $letrasFalladas = $_POST['letras_falladas'] ?? 0;
                $palabraAcertada = isset($_POST['palabra_acertada']) && ($_POST['palabra_acertada'] == '1' || $_POST['palabra_acertada'] === 'true') ? 1 : 0;
                $puntuacionObtenida = $_POST['puntuacion_obtenida'] ?? 0;

                $modeloJuego = new M_juego();
                $result = $modeloJuego->finalizarPartida($nombreUsuario, $idPalabraJugada, $letrasAcertadas, $letrasFalladas, $palabraAcertada, $puntuacionObtenida);

                if ($result['success']) {
                    echo json_encode(['success' => true, 'message' => $result['message'], 'redirect' => '../vistas/juego/V_configurar.php']);
                } else {
                    echo json_encode(['success' => false, 'message' => $result['message']]);
                }
                break;

            case 'logout':
                session_unset();
                session_destroy();
                echo json_encode(['success' => true, 'message' => 'Sesión cerrada correctamente.', 'redirect' => '../vistas/autentificarse/V_login.php']);
                exit;
                break;

            default:
                echo 'Acción no soportada';
                exit;
        }
    } else {
        echo 'Acción no especificada';
        exit;
    }
} else {
    // Si la solicitud no es POST o no se maneja ninguna acción, se considera una acción no soportada.
    http_response_code(400);
    echo 'Acción no soportada';
}
