<?php
include "conn.php";

// 1. Hacemos la consulta a la base de datos
$sql = "SELECT id_categoria, nombre_categoria FROM categorias";
$resultado = $conn->query($sql);

// 2. Metemos los datos en un array de PHP
$datos = [];
while($fila = $resultado->fetch_assoc()) {
    $datos[] = $fila;
}

// 3. Lo soltamos todo en formato JSON para que JavaScript lo entienda
echo json_encode($datos);

$conn->close();
?>
