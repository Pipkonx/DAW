<?php
include "conn.php"; // Conexión a la base de datos

$metodo = $_SERVER['REQUEST_METHOD'];

// 1. LISTAR CATEGORÍAS (GET)
if ($metodo === 'GET') {
    $resultado = $conn->query("SELECT * FROM categorias");
    $datos = [];
    
    while ($fila = $resultado->fetch_assoc()) {
        $datos[] = $fila;
    }
    
    echo json_encode($datos);
}

// 2. AGREGAR CATEGORÍA (POST)
if ($metodo === 'POST') {
    $entrada = json_decode(file_get_contents("php://input"), true);
    $nombre = $entrada['nombre'];

    // Preparamos la frase SQL, usando el "?" como un hueco o sitio reservado
    $stmt = $conn->prepare("INSERT INTO categorias (nombre) VALUES (?)");
    
    // El "bind_param" sirve para llenar ese hueco (?) con seguridad.
    // La "s" significa que el dato es un Texto (String).
    $stmt->bind_param("s", $nombre);
    
    // Ejecutamos la orden final
    $stmt->execute();

    echo json_encode(["mensaje" => "Agregado correctamente"]);
}

// 3. ELIMINAR CATEGORÍA (DELETE)
if ($metodo === 'DELETE') {
    $id = $_GET['id'];

    // Preparamos el borrado con un hueco (?) para el ID
    $stmt = $conn->prepare("DELETE FROM categorias WHERE id = ?");
    
    // Llenamos el hueco con el ID.
    // La "i" significa que el dato es un Número Entero (Integer).
    $stmt->bind_param("i", $id);
    
    // Ejecutamos la orden
    $stmt->execute();

    echo json_encode(["mensaje" => "Eliminado correctamente"]);
}

// 4. MODIFICAR CATEGORÍA (PUT)
if ($metodo === 'PUT') {
    $entrada = json_decode(file_get_contents("php://input"), true);
    $id = $entrada['id'];
    $nombre = $entrada['nombre'];

    // Preparamos la actualización
    $stmt = $conn->prepare("UPDATE categorias SET nombre = ? WHERE id = ?");
    
    // "s" para el nombre (texto), "i" para el id (número)
    $stmt->bind_param("si", $nombre, $id);
    $stmt->execute();

    echo json_encode(["mensaje" => "Modificado correctamente"]);
}
?>