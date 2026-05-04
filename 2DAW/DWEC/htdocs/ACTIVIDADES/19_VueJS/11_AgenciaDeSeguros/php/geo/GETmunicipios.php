<?php
// Configuración de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');
include '../config/conexion.php';

// Aceptamos el ID de la provincia
$id_provincia = 0;
if (isset($_GET['provincia'])) {
    $id_provincia = intval($_GET['provincia']);
} elseif (isset($_GET['id_provincia'])) {
    $id_provincia = intval($_GET['id_provincia']);
}

$datos = array();

if ($id_provincia > 0) {
    // Consulta directa para máxima compatibilidad (sanitizada con intval)
    // Filtramos municipios que pertenecen a la provincia (ID / 1000)
    $sql = "SELECT id, municipio FROM municipios WHERE FLOOR(id / 1000) = $id_provincia ORDER BY municipio ASC";
    $resultado = $conn->query($sql);
    
    if ($resultado) {
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }
    }
} else {
    // Si no hay provincia, devolvemos todos
    $sql = "SELECT id, municipio FROM municipios ORDER BY municipio ASC";
    $resultado = $conn->query($sql);
    if ($resultado) {
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }
    }
}

echo json_encode($datos);
$conn->close();
?>
