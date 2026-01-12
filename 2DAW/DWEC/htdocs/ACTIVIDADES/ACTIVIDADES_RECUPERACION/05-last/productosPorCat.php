<?php
// 1. Conectamos a la base de datos
include "conn.php";

// 2. Recogemos el ID de la categoría que nos envía JavaScript a través de la URL (ej: ?id_cat=1)
$id = $_GET['id_cat'];

// 3. Preparamos la consulta SQL para buscar solo los productos de esa categoría
$sql = "SELECT id_producto, nombre_producto, precio FROM productos WHERE id_categoria = $id";

// 4. Ejecutamos la consulta
$resultado = $conn->query($sql);

// 5. Creamos una lista vacía para los productos
$lista = [];

// 6. Si encontramos productos, los metemos en nuestra lista
if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $lista[] = $fila;
    }
}

// 7. Lo enviamos todo en formato JSON (como un texto estructurado) para JavaScript
echo json_encode($lista);

// 8. Cerramos la conexión
$conn->close();
?>
