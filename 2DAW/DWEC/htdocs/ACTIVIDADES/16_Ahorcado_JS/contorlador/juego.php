<?php
require_once __DIR__ . '/../conexion/config.php';
require_once __DIR__ . '/../modelo/Categoria.php';
require_once __DIR__ . '/../modelo/Palabra.php';
require_once __DIR__ . '/../modelo/Jugador.php';
require_once __DIR__ . '/../modelo/Partida.php';

$pdo = Database::getInstance()->getConnection();
$categoriaModel = new Categoria();
$palabraModel = new Palabra();
$jugadorModel = new Jugador();
$partidaModel = new Partida();

// CORS es un mecanismo de seguridad en los navegadores que permie a una web solicitar recursos de un dominio diferente
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function json_response($data, $code = 200)
{
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// GET: listado de categorías
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    if ($action === 'categorias') {
        $cats = $categoriaModel->all();
        json_response($cats);
    }
}

// POST: inicio y finalización de partidas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'start_json') {
        $login = trim($_POST['login'] ?? '');
        $categoria = isset($_POST['categoria']) ? intval($_POST['categoria']) : 0;
        $dificultad = $_POST['dificultad'] ?? '';

        if ($login === '' || $categoria <= 0 || $dificultad === '') {
            json_response(['error' => 'Datos inválidos'], 400);
        }

        // dificultad -> condición de longitud y máximo de fallos
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
                json_response(['error' => 'Dificultad inválida'], 400);
        }

        $pal = $palabraModel->randomByCategoriaAndDificultad($categoria, $dificultad);
        if (!$pal) {
            json_response(['error' => 'No hay palabras para esa categoría/dificultad'], 404);
        }

        json_response([
            'id_palabra' => intval($pal['id_palabra']),
            'palabra' => $pal['texto_palabra'],
            'maxfallos' => $maxFallos,
            'login' => $login,
        ]);
    }

    if ($action === 'finalizar_json') {
        $login = trim($_POST['login'] ?? '');
        $idPalabra = intval($_POST['id_palabra'] ?? 0);
        $letrasAcertadas = intval($_POST['letras_acertadas'] ?? 0);
        $letrasFalladas = intval($_POST['letras_falladas'] ?? 0);
        $palabraAcertada = isset($_POST['palabra_acertada']) && ($_POST['palabra_acertada'] == '1' || $_POST['palabra_acertada'] === 'true') ? 1 : 0;
        $puntuacion = intval($_POST['puntuacion_obtenida'] ?? 0);

        if ($login === '' || $idPalabra <= 0) {
            json_response(['error' => 'Datos inválidos'], 400);
        }

        // Obtener id_jugador
        $idJugador = $jugadorModel->getIdByLogin($login);
        if ($idJugador === null) {
            json_response(['error' => 'Jugador no encontrado'], 404);
        }

        // Insertar partida
        $fecha = date('Y-m-d H:i:s');
        $partidaModel->insert($idJugador, $idPalabra, $fecha, $letrasAcertadas, $letrasFalladas, $palabraAcertada, $puntuacion);

        json_response(['ok' => true, 'message' => 'Partida guardada']);
    }
}

json_response(['error' => 'Acción no soportada'], 400);
