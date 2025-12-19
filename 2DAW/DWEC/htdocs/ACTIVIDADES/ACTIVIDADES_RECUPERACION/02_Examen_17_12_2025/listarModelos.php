<?php
// Encabezado para indicar que la respuesta es JSON
header('Content-Type: application/json; charset=utf-8');

// Incluir el archivo de conexión
include 'conexion.php'; // o require 'conexion.php';

// Verificar conexión
if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}

// Verificar si se ha recibido el parámetro marca_id
if (!isset($_GET['id_marca'])) {
    echo json_encode(["error" => "Parámetro 'id_marca' no proporcionado."]);
    $conexion->close();
    exit();
}

// Capturamos el id de la marca seleccionada y lo convertimos a entero para seguridad básica
$marcaSeleccionada = (int)$_GET['id_marca'];

// Consulta SQL
$sql = "SELECT id, nombre, precio FROM modelos WHERE id_marca = $marcaSeleccionada";
$resultado = $conexion->query($sql);

// Array para guardar los resultados
$modelos = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $modelos[] = $fila;
    }
    // Devolver los datos como JSON
    echo json_encode($modelos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
    echo json_encode(["mensaje" => "No se encontraron modelos para la marca seleccionada."]);
}

// Cerrar conexión
$conexion->close();
