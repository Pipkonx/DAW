<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../conexion/Conexion.php';

try {
    $pdo = Conexion::getInstance()->getConnection();

    $sql = 'SELECT id, nombre, apellido, nota FROM alumnos ORDER BY id';
    $stmt = $pdo->query($sql);
    $alumnos = $stmt->fetchAll();

    echo json_encode($alumnos);
} catch (Exception $e) {
    echo json_encode(['error' => 'Error al listar alumnos', 'detalle' => $e->getMessage()]);
}