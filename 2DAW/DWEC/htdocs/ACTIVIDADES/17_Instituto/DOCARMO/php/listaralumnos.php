<?php
include 'conexion.php';

try {
    $sql = "SELECT * FROM alumnos";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver JSON limpio
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($alumnos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => $e->getMessage()]);
}

$conn = null;
