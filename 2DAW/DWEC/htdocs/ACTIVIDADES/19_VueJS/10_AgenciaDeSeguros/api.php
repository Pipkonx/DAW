<?php
/**
 * API REST completa para la Agencia de Seguros
 */
require_once 'db_config.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            handleGet($pdo, $action);
            break;
        case 'POST':
            handlePost($pdo, $action);
            break;
        case 'DELETE':
            handleDelete($pdo, $action);
            break;
        default:
            echo json_encode(['error' => 'Método no soportado']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

function handleGet($pdo, $action) {
    switch ($action) {
        case 'clientes':
            $stmt = $pdo->query("SELECT * FROM 10_agenciaseguros_clientes ORDER BY nombre ASC");
            echo json_encode($stmt->fetchAll());
            break;
        
        case 'polizas':
            // Si viene cliente_id, filtramos
            $cliente_id = $_GET['cliente_id'] ?? null;
            if ($cliente_id) {
                $stmt = $pdo->prepare("SELECT * FROM 10_agenciaseguros_polizas WHERE cliente_id = ? ORDER BY fecha DESC");
                $stmt->execute([$cliente_id]);
            } else {
                // Todas las pólizas con nombre de cliente para la vista general
                $stmt = $pdo->query("SELECT p.*, c.nombre as cliente_nombre 
                                   FROM 10_agenciaseguros_polizas p 
                                   JOIN 10_agenciaseguros_clientes c ON p.cliente_id = c.id 
                                   ORDER BY p.fecha DESC");
            }
            echo json_encode($stmt->fetchAll());
            break;

        case 'pagos':
            $poliza_id = $_GET['poliza_id'] ?? null;
            if ($poliza_id) {
                $stmt = $pdo->prepare("SELECT * FROM 10_agenciaseguros_pagos WHERE poliza_id = ? ORDER BY fecha ASC");
                $stmt->execute([$poliza_id]);
                echo json_encode($stmt->fetchAll());
            }
            break;

        case 'provincias':
            $stmt = $pdo->query("SELECT * FROM provincias ORDER BY provincia ASC");
            echo json_encode($stmt->fetchAll());
            break;

        case 'municipios':
            $id_provincia = $_GET['id_provincia'] ?? 0;
            if ($id_provincia) {
                // Lógica compatible con la estructura de la BD de municipios
                $stmt = $pdo->prepare("SELECT * FROM municipios WHERE (LENGTH(id) = 4 AND LEFT(id, 1) = ?) OR (LENGTH(id) = 5 AND LEFT(id, 2) = ?) ORDER BY municipio ASC");
                $stmt->execute([$id_provincia, $id_provincia]);
                echo json_encode($stmt->fetchAll());
            } else {
                echo json_encode([]);
            }
            break;

        default:
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
}

function handlePost($pdo, $action) {
    $input = json_decode(file_get_contents('php://input'), true);

    switch ($action) {
        case 'login':
            $stmt = $pdo->prepare("SELECT * FROM 10_agenciaseguros_usuarios WHERE username = ? AND password = ?");
            $stmt->execute([$input['username'], $input['password']]);
            $user = $stmt->fetch();
            if ($user) {
                unset($user['password']);
                echo json_encode(['success' => true, 'user' => $user]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
            }
            break;

        case 'crear_cliente':
            $sql = "INSERT INTO 10_agenciaseguros_clientes (codigo, nombre, telefono, localidad, cp, provincia, tipo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $input['codigo'], $input['nombre'], $input['telefono'], 
                $input['localidad'], $input['cp'], $input['provincia'], $input['tipo']
            ]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;

        case 'crear_poliza':
            $sql = "INSERT INTO 10_agenciaseguros_polizas (cliente_id, numero, importe, fecha, estado, observaciones) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $input['cliente_id'], $input['numero'], $input['importe'], 
                $input['fecha'], $input['estado'], $input['observaciones']
            ]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
            break;

        case 'editar_cliente':
            $sql = "UPDATE 10_agenciaseguros_clientes 
                    SET codigo = ?, nombre = ?, telefono = ?, localidad = ?, cp = ?, provincia = ?, tipo = ? 
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $input['codigo'], $input['nombre'], $input['telefono'], 
                $input['localidad'], $input['cp'], $input['provincia'], $input['tipo'],
                $input['id']
            ]);
            echo json_encode(['success' => true]);
            break;

        case 'crear_pago':
            // Validar que el pago no supere el importe de la póliza
            $stmt = $pdo->prepare("SELECT importe FROM 10_agenciaseguros_polizas WHERE id = ?");
            $stmt->execute([$input['poliza_id']]);
            $poliza = $stmt->fetch();
            
            $stmt = $pdo->prepare("SELECT SUM(importe) as total FROM 10_agenciaseguros_pagos WHERE poliza_id = ?");
            $stmt->execute([$input['poliza_id']]);
            $pagos = $stmt->fetch();
            
            $totalActual = $pagos['total'] ?? 0;
            if (($totalActual + $input['importe']) > $poliza['importe']) {
                echo json_encode(['success' => false, 'message' => 'El importe total supera el valor de la póliza']);
            } else {
                $stmt = $pdo->prepare("INSERT INTO 10_agenciaseguros_pagos (poliza_id, fecha, importe) VALUES (?, ?, ?)");
                $stmt->execute([$input['poliza_id'], $input['fecha'], $input['importe']]);
                echo json_encode(['success' => true]);
            }
            break;

        default:
            echo json_encode(['error' => 'Acción no válida']);
            break;
    }
}

function handleDelete($pdo, $action) {
    $id = $_GET['id'] ?? null;
    if (!$id) exit;

    switch ($action) {
        case 'cliente':
            $stmt = $pdo->prepare("DELETE FROM 10_agenciaseguros_clientes WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;
    }
}
?>
