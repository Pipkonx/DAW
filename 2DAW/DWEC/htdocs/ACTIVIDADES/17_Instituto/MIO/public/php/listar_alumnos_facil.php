<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../conexion/Conexion.php';

// try {
//     $pdo = Conexion::getInstance()->getConnection();

//     $sql = 'SELECT * FROM alumnos';
//     $stmt = $pdo->query($sql);
//     $alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

//     echo json_encode($alumnos);
// } catch (Exception $e) {
//     echo json_encode(['error' => 'Error al listar alumnos', 'detalle' => $e->getMessage()]);
// }



// Incluir el archivo de conexión
include 'conexion.php'; // o require 'conexion.php';

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}

// Consulta SQL
$sql = "SELECT * FROM alumnos";
$resultado = $conn->query($sql);
$cadena = "";
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $cadena = $cadena."<li>".htmlspecialchars($fila['nombre']) . "</li>";
        }
    } else {
        echo "<tr><td colspan='3'>No hay registros en la tabla alumnos.</td></tr>";
    }
    echo $cadena;
    $conn->close();
    ?>