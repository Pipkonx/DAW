<?php

header('Content-Type: application/json; charset=utf-8');

include 'conexion.php';

if ($conexion->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $conexion->connect_error]);
    exit();
}

$nombre    = $_GET['nombre'];
$apellidos = $_GET['apellidos'];
$nota      = $_GET['nota']; // Esta nota será la primera nota del alumno, se insertará como la primera nota de una asignatura por defecto.

// Insertar el alumno en la tabla 'alumnos' con nota inicial 0
$sqlAlumno = "INSERT INTO alumnos (nombre, apellidos, nota) VALUES (?, ?, ?)";
$stmtAlumno = $conexion->prepare($sqlAlumno);
$stmtAlumno->bind_param("ssd", $nombre, $apellidos, $nota);

if ($stmtAlumno->execute()) {
    $idAlumno = $conexion->insert_id;
    $stmtAlumno->close();

    // Insertar la primera nota en la tabla 'notas' con una asignatura por defecto (ej. 'General')
    $asignaturaDefault = 'General'; // O puedes dejar que el usuario la añada después
    $sqlNota = "INSERT INTO notas (codigoAlumno, Asignatura, nota) VALUES (?, ?, ?)";
    $stmtNota = $conexion->prepare($sqlNota);
    $stmtNota->bind_param("isi", $idAlumno, $asignaturaDefault, $nota);

    if ($stmtNota->execute()) {
        $stmtNota->close();

        // Recalcular la nota media del alumno (que será la primera nota por ahora)
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

        echo json_encode([
            "status" => "success",
            "message" => "Alumno y primera nota insertados correctamente.",
            "data" => [
                "codigo" => $idAlumno,
                "nombre" => $nombre,
                "apellidos" => $apellidos,
                "asignatura" => $asignaturaDefault,
                "nota" => $nota
            ]
        ]);
    } else {
        // Si falla la inserción de la nota, eliminar el alumno recién creado para evitar inconsistencias
        $sqlDeleteAlumno = "DELETE FROM alumnos WHERE codigo = ?";
        $stmtDeleteAlumno = $conexion->prepare($sqlDeleteAlumno);
        $stmtDeleteAlumno->bind_param("i", $idAlumno);
        $stmtDeleteAlumno->execute();
        $stmtDeleteAlumno->close();

        echo json_encode([
            "status" => "error",
            "message" => "Error al insertar la nota: " . $stmtNota->error
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Error al insertar el alumno: " . $stmtAlumno->error
    ]);
}

$conexion->close();
?>