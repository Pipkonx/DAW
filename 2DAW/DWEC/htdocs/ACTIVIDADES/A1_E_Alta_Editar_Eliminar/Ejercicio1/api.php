<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'getAllPoblaciones':
            $stmt = $pdo->query('SELECT * FROM poblaciones');
            $poblaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($poblaciones);
            break;
        case 'getPoblacionByCp':
            $cp = $_POST['cp'] ?? null;
            if ($cp) {
                $stmt = $pdo->prepare('SELECT * FROM poblaciones WHERE cp = ?');
                $stmt->execute([$cp]);
                $poblacion = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($poblacion);
            } else {
                echo json_encode([]); // Devolver un array vacío si no se encuentra o falta CP
            }
            break;
        case 'getPoblacionByNombre':
            $nombre = $_POST['nombre'] ?? null;
            if ($nombre) {
                $stmt = $pdo->prepare('SELECT * FROM poblaciones WHERE nombre = ?');
                $stmt->execute([$nombre]);
                $poblacion = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode($poblacion);
            } else {
                echo json_encode([]); // Devolver un array vacío si no se encuentra o falta nombre
            }
            break;
        case 'updateHabitantes':
            $cp = $_POST['cp'] ?? null;
            $habitantes = $_POST['habitantes'] ?? null;

            if ($cp && $habitantes !== null) {
                $stmt = $pdo->prepare('UPDATE poblaciones SET habitantes = ? WHERE cp = ?');
                $success = $stmt->execute([$habitantes, $cp]);
                echo json_encode(['success' => $success]); // Solo indicar éxito o fracaso
            } else {
                echo json_encode(['success' => false]); // Indicar fracaso si faltan datos
            }
            break;
        default:
            echo json_encode([]); // Devolver un array vacío para acciones no válidas
            break;
    }
} else {
    echo json_encode([]); // Devolver un array vacío para solicitudes no POST
}
