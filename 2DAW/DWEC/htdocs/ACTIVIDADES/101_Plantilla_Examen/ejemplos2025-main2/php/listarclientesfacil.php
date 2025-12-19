<?php


// Incluir el archivo de conexión
include 'conexion.php'; // o require 'conexion.php';

// Verificar conexión
if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conexion->connect_error]));
}

// Consulta SQL
$sql = "SELECT * FROM clientes";
$resultado = $conexion->query($sql);
$cadena = "";
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $cadena = $cadena."<li>".htmlspecialchars($fila['nombre']) . " " . htmlspecialchars($fila['apellidos']) . "</li>";
        }
    } else {
        echo "<tr><td colspan='3'>No hay registros en la tabla clientes.</td></tr>";
    }
    echo $cadena;
    $conexion->close();
    ?>
