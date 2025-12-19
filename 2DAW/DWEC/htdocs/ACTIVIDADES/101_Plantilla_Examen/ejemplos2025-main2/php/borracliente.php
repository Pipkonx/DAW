<?php

// Encabezado para indicar que la respuesta es JSON
header('Content-Type: application/json; charset=utf-8');

// Incluir el archivo de conexión
include 'conexion.php'; // o require 'conexion.php';

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}

$id_cliente = $_GET['id'] ?? null;

if ($id_cliente === null) {
    echo json_encode(['status' => 'error', 'message' => 'ID de cliente no proporcionado']);
    exit();
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // Eliminar mascotas asociadas al cliente
    $sql_delete_mascotas = "DELETE FROM mascotas WHERE id_cliente = ?";
    $stmt_mascotas = $conn->prepare($sql_delete_mascotas);
    $stmt_mascotas->bind_param("i", $id_cliente);
    $stmt_mascotas->execute();
    $stmt_mascotas->close();

    // Eliminar el cliente
    $sql_delete_cliente = "DELETE FROM clientes WHERE id = ?";
    $stmt_cliente = $conn->prepare($sql_delete_cliente);
    $stmt_cliente->bind_param("i", $id_cliente);
    $stmt_cliente->execute();
    $stmt_cliente->close();

    // Confirmar la transacción
    $conn->commit();

    echo json_encode([
        "status" => "success",
        "message" => "Cliente y sus mascotas borrados correctamente.",
    ]);

} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    echo json_encode([
        "status" => "error",
        "message" => "Error al borrar cliente y/o mascotas: " . $e->getMessage()
    ]);
}

$conn->close();
?>