<?php
// Convertir YYYY-MM-DD → dd/mm/YYYY para mostrar
function cambiarFormato($fechaSinConvertir) {
    if (!$fechaSinConvertir) return '';
    $partes = explode('-', $fechaSinConvertir);
    return $partes[2] . '/' . $partes[1] . '/' . $partes[0];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tarea Creada</title>
</head>
<body>
    <h1>✅ Tarea creada correctamente</h1>
    <p><strong>NIF/CIF:</strong> <?= htmlspecialchars($nifCif) ?></p>
    <p><strong>Persona:</strong> <?= htmlspecialchars($personaNombre) ?></p>
    <p><strong>Teléfono:</strong> <?= htmlspecialchars($telefono) ?></p>
    <p><strong>Descripción:</strong> <?= htmlspecialchars($descripcionTarea) ?></p>
    <p><strong>Correo:</strong> <?= htmlspecialchars($correo) ?></p>
    <p><strong>Dirección:</strong> <?= htmlspecialchars($direccionTarea) ?></p>
    <p><strong>Población:</strong> <?= htmlspecialchars($poblacion) ?></p>
    <p><strong>Código postal:</strong> <?= htmlspecialchars($codigoPostal) ?></p>
    <p><strong>Provincia:</strong> <?= htmlspecialchars($provincia) ?></p>
    <p><strong>Estado:</strong> <?= htmlspecialchars($estadoTarea) ?></p>
    <p><strong>Operario:</strong> <?= htmlspecialchars($operarioEncargado) ?></p>
    <p><strong>Fecha realización:</strong> <?= htmlspecialchars(cambiarFormato($fechaRealizacion)) ?></p>
    <p><strong>Anotaciones anteriores:</strong> <?= htmlspecialchars($anotacionesAnteriores) ?></p>
    <p><strong>Anotaciones posteriores:</strong> <?= htmlspecialchars($anotacionesPosteriores) ?></p>

    <br>
    <a href="alta_tarea.php">Crear otra tarea</a><br>
    <a href="index.php">Volver al inicio</a>
</body>
</html>