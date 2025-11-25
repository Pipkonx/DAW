<?php
header('Content-Type: application/json; charset=utf-8');

include 'conexion.php';

if ($conexion->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conexion->connect_error]);
    exit();
}

$idAlumno = $_GET['id'];
$asignatura = $_GET['asignatura'];
$nota = $_GET['nota'];

// Preparar la consulta SQL para actualizar la nota
// Asumiendo que tienes una tabla 'notas' con columnas 'codigoAlumno', 'Asignatura', 'nota'
$sql = "UPDATE notas SET nota = ? WHERE codigoAlumno = ? AND Asignatura = ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("sis", $nota, $idAlumno, $asignatura);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
    // Recalcular la nota media del alumno
    $sqlMedia = "SELECT AVG(nota) AS media FROM notas WHERE codigoAlumno = ?";
    $stmtMedia = $conexion->prepare($sqlMedia);
    $stmtMedia->bind_param("i", $idAlumno);
    $stmtMedia->execute();
    $resultadoMedia = $stmtMedia->get_result();
    $filaMedia = $resultadoMedia->fetch_assoc();
    $nuevaNotaMedia = $filaMedia['media'];
    $stmtMedia->close();

    // Actualizar la nota media en la tabla de alumnos
    $sqlActualizarAlumno = "UPDATE alumnos SET nota = ? WHERE codigo = ?";
    $stmtActualizarAlumno = $conexion->prepare($sqlActualizarAlumno);
    $stmtActualizarAlumno->bind_param("di", $nuevaNotaMedia, $idAlumno);
    $stmtActualizarAlumno->execute();
    $stmtActualizarAlumno->close();

    echo json_encode(['success' => true, 'message' => 'Nota y nota media actualizadas correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontró la nota para actualizar o los datos son los mismos.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la nota: ' . $stmt->error]);
}

$stmt->close();
$conexion->close();
?>