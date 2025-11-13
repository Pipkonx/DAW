<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Alta de Tarea</title>
    <style>
        .error {
            color: red;
        }
    </style>
</head>

<body>
    <h1>Alta de Tarea</h1>

    <form action="alta_tarea.php" method="POST" enctype="multipart/form-data">
        <label>NIF/CIF:</label><br>
        <input type="text" name="nifCif" value="<?= htmlspecialchars($nifCif) ?>"><br>
        <?php verErrores($errores, 'nif_cif') ?><br>

        <label>Persona de contacto:</label><br>
        <input type="text" name="personaNombre" value="<?= htmlspecialchars($personaNombre) ?>"><br>
        <?php verErrores($errores, 'nombre_persona') ?><br>

        <label>Teléfono:</label><br>
        <input type="text" name="telefono" value="<?= htmlspecialchars($telefono) ?>"><br>
        <?php verErrores($errores, 'telefono') ?><br>

        <label>Correo electrónico:</label><br>
        <input type="text" name="correo" value="<?= htmlspecialchars($correo) ?>"><br>
        <?php verErrores($errores, 'correo') ?><br>

        <label>Descripción de la tarea:</label><br>
        <textarea name="descripcionTarea"><?= htmlspecialchars($descripcionTarea) ?></textarea><br>
        <?php verErrores($errores, 'descripcion_tarea') ?><br>

        <label>Dirección:</label><br>
        <input type="text" name="direccionTarea" value="<?= htmlspecialchars($direccionTarea) ?>"><br><br>

        <label>Población:</label><br>
        <input type="text" name="poblacion" value="<?= htmlspecialchars($poblacion) ?>"><br><br>

        <label>Código Postal:</label><br>
        <input type="text" name="codigoPostal" value="<?= htmlspecialchars($codigoPostal) ?>"><br><br>

        <label>Provincia:</label><br>
        <select name="provincia">
            <option value="">Seleccione provincia</option>
            <?php mostrarProvincias($provincia) ?>
        </select><br>
        <?php verErrores($errores, 'provincia') ?><br>

        <label>Estado:</label><br>
        <select name="estadoTarea">
            <option value="B" <?= $estadoTarea == "B" ? "selected" : "" ?>>Esperando ser aprobada</option>
            <option value="P" <?= $estadoTarea == "P" ? "selected" : "" ?>>Pendiente</option>
            <option value="R" <?= $estadoTarea == "R" ? "selected" : "" ?>>Realizada</option>
            <option value="C" <?= $estadoTarea == "C" ? "selected" : "" ?>>Cancelada</option>
        </select><br><br>

        <label>Operario encargado:</label><br>
        <select name="operarioEncargado">
            <option value="">Seleccione operario</option>
            <option value="Juan Pérez" <?= $operarioEncargado == "Juan Pérez" ? "selected" : "" ?>>Juan Pérez</option>
            <option value="María López" <?= $operarioEncargado == "María López" ? "selected" : "" ?>>María López</option>
            <option value="Carlos Ruiz" <?= $operarioEncargado == "Carlos Ruiz" ? "selected" : "" ?>>Carlos Ruiz</option>
            <option value="Ana María Fernández" <?= $operarioEncargado == "Ana María Fernández" ? "selected" : "" ?>>Ana María Fernández</option>
            <option value="Sara Martínez" <?= $operarioEncargado == "Sara Martínez" ? "selected" : "" ?>>Sara Martínez</option>
            <option value="Lucía Hurtado" <?= $operarioEncargado == "Lucía Hurtado" ? "selected" : "" ?>>Lucía Hurtado</option>
        </select><br><br>

        <label>Fecha de realización:</label><br>
        <input type="date" name="fechaRealizacion" value="<?= htmlspecialchars($fechaRealizacion) ?>"><br>
        <?php verErrores($errores, 'fecha_realizacion') ?><br>

        <label for="anotacionesAnteriores">Anotaciones anteriores:</label><br>
        <textarea id="anotacionesAnteriores" name="anotacionesAnteriores"><?= htmlspecialchars($anotacionesAnteriores) ?></textarea><br><br>

        <label for="fichero_resumen">Fichero resumen:</label>
        <input type="file" id="fichero_resumen" name="fichero_resumen"><br><br>

        <label for="fotos">Fotos del trabajo:</label>
        <input type="file" id="fotos" name="fotos[]" multiple><br><br>

        <input type="submit" value="Crear tarea">
        <br><br>
        <a href="../index.php">Cancelar</a>
    </form>
</body>

</html>