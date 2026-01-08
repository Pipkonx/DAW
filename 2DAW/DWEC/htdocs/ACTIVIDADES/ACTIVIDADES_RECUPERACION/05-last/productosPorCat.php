<?php
include "conn.php";

// 1. Cogemos el ID que nos manda JavaScript desde la URL
$id = $_GET['id_cat'];

// 2. Buscamos los productos de esa categoría
$sql = "SELECT id_producto, nombre_producto, precio FROM productos WHERE id_categoria = $id";
$resultado = $conn->query($sql);

// 3. Metemos los resultados en una lista
$lista = [];
while($fila = $resultado->fetch_assoc()) {
    $lista[] = $fila;
}

// 4. Lo enviamos todo como JSON
echo json_encode($lista);

$conn->close();
?>
