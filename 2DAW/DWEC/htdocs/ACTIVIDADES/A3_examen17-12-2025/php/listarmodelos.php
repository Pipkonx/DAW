<?php
// // Encabezado para indicar que la respuesta es JSON
// header('Content-Type: application/json; charset=utf-8');

//Incluir el archivo de conexión
include 'conexion.php'; // o require 'conexion.php';

// Verificar conexión
if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conexion->connect_error]));
}

// Consulta SQL
$sql = "SELECT * FROM modelos";
$resultado = $conexion->query($sql);
$cadena = "";
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $cadena = $cadena."<option>".htmlspecialchars($fila['nombre']) . "</option>";
        }
    } else {
    }
    echo $cadena;
    $conexion->close();

// en forma de json
// $resultado = $conexion->query($sql);

// // Array para guardar los resultados
// $modelos = [];

// if ($resultado && $resultado->num_rows > 0) {
//     while ($fila = $resultado->fetch_assoc()) {
//         $modelos[] = $fila;
//     }
//     // Devolver los datos como JSON
//     echo json_encode($modelos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
// } else {
//     echo json_encode(["mensaje" => "No hay registros en la tabla modelos"]);
// }

// // Cerrar conexión
// $conexion->close();
?>
