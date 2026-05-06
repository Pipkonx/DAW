<?php
require 'conexion.php';

$method = $_SERVER['REQUEST_METHOD'];
$accion = $_GET['accion'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

if ($accion === 'trabajadores') {
    if ($method === 'GET') {
        $stmt = $pdo->query("select t.*, 
		count(ta.id) as num_tareas 
		from trabajadores t 
		LEFT JOIN tareas ta ON t.id = ta.trabajador_id 
		GROUP BY t.id");
        echo json_encode($stmt->fetchAll());
    } elseif ($method === 'POST') {
        $stmt = $pdo->prepare("insert INTO trabajadores (nombre, email, puesto) values (?, ?, ?)");
        $stmt->execute([$input['nombre'], $input['email'], $input['puesto']]);
        echo json_encode(['success' => true]);
    } elseif ($method === 'DELETE') {
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM trabajadores WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    }
} elseif ($accion === 'tareas') {
    if ($method === 'GET') {
        $trabajador_id = $_GET['trabajador_id'] ?? 0;
        $stmt = $pdo->prepare("select * from tareas where trabajador_id = ?");
        $stmt->execute([$trabajador_id]);
        echo json_encode($stmt->fetchAll());
    } elseif ($method === 'POST') {
        $trabajador_id = $_GET['trabajador_id'] ?? 0;
        $stmt = $pdo->prepare("insert into tareas (titulo, descripcion, estado, prioridad, trabajador_id) values (?, ?, ?, ?, ?)");
        $stmt->execute([$input['titulo'], $input['descripcion'], $input['estado'] ?? 'pendiente', $input['prioridad'] ?? 'media', $trabajador_id]);
        echo json_encode(['success' => true]);
    } elseif ($method === 'PUT') {
        $id = $_GET['id'] ?? 0;
        if (isset($_GET['cambiar_estado'])) {
             $stmt = $pdo->prepare("update tareas SET estado = ? WHERE id = ?");
             $stmt->execute([$input['estado'], $id]);
        } else {
            $stmt = $pdo->prepare("update tareas SET titulo = ?, descripcion = ?, estado = ?, prioridad = ? WHERE id = ?");
            $stmt->execute([$input['titulo'], $input['descripcion'], $input['estado'], $input['prioridad'], $id]);
        }
        echo json_encode(['success' => true]);
    } elseif ($method === 'DELETE') {
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM tareas WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    }
}
?>
