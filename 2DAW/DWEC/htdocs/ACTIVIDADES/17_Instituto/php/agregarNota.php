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

// Verificar si ya existe una nota para esa asignatura y alumno
$sqlCheck = "SELECT COUNT(*) FROM notas WHERE codigoAlumno = ? AND Asignatura = ?";
$stmtCheck = $conexion->prepare($sqlCheck);
// el bind_param es para enlazar los valores de $idAlumno y $asignatura a la consulta SQL
$stmtCheck->bind_param("is", $idAlumno, $asignatura);
$stmtCheck->execute();
$stmtCheck->bind_result($count);
$stmtCheck->fetch();
$stmtCheck->close();

if ($count > 0) {
    echo json_encode(['success' => false, 'message' => 'Ya existe una nota para esta asignatura. Utilice "Actualizar Nota" para modificarla.']);
    exit();
}

// Preparar la consulta SQL para insertar la nueva nota
$sql = "INSERT INTO notas (codigoAlumno, Asignatura, nota) VALUES (?, ?, ?)";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("isi", $idAlumno, $asignatura, $nota);

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

        echo json_encode(['success' => true, 'message' => 'Nota agregada y nota media actualizada correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo agregar la nota.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error al agregar la nota: ' . $stmt->error]);
}

$stmt->close();
$conexion->close();
