<?php
// 1. Conectamos a la base de datos (usando el archivo que ya tiene la conexión)
include "conn.php";

// 2. Preparamos la frase que le diremos a la base de datos (SQL)
$sql = "SELECT id_categoria, nombre_categoria FROM categorias";

// 3. Ejecutamos la frase y guardamos el resultado
$resultado = $conn->query($sql);

// 4. Creamos una lista (array) vacía para guardar los datos
$datos = [];

// 5. Si hay resultados, los vamos metiendo uno a uno en nuestra lista
if ($resultado) {
    while ($fila = $resultado->fetch_assoc()) {
        $datos[] = $fila; // Añadimos cada fila a la lista
    }
}

// 6. Convertimos la lista de PHP a un formato que JavaScript entienda (JSON)
// Esto es lo que "leerá" el fetch desde JavaScript
echo json_encode($datos);

// 7. Cerramos la conexión para no dejarla abierta
$conn->close();
?>
