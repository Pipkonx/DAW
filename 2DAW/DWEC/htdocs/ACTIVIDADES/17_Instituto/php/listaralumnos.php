<?php
header('Content-Type: application/json; charset=utf-8');

include 'conexion.php';

// Verificar conexión
if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}

// Consulta SQL
// Con el avg hacemos la media de notas para que las muestre con 2 decimales en base a las notas
$sql = "SELECT a.codigo, a.nombre, a.apellidos, AVG(n.nota) AS nota_media FROM alumnos a LEFT JOIN notas n ON a.codigo = n.codigoAlumno GROUP BY a.codigo, a.nombre, a.apellidos";
$resultado = $conexion->query($sql);

// Array para guardar los resultados
$alumnos = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $alumnos[] = $fila;
    }
    // Devolver los datos como JSON
    echo json_encode($alumnos, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
    echo json_encode(["mensaje" => "No hay registros en la tabla alumnos."]);
}

// Cerrar conexión
$conexion->close();
