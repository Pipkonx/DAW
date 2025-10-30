<?php require "views/templates/header.php"; ?>

<h2>Editar tarea #<?= $tarea['id'] ?></h2>

<form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Persona de contacto</label>
        <input type="text" name="persona_contacto" value="<?= htmlspecialchars($tarea['persona_contacto']) ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Teléfono</label>
        <input type="text" name="telefono" value="<?= htmlspecialchars($tarea['telefono']) ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Descripción</label>
        <textarea name="descripcion" class="form-control" required><?= htmlspecialchars($tarea['descripcion']) ?></textarea>
    </div>

    <div class="mb-3">
        <label>Correo electrónico</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($tarea['correo']) ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Dirección</label>
        <input type="text" name="direccion" value="<?= htmlspecialchars($tarea['direccion']) ?>" class="form-control">
    </div>

    <div class="mb-3">
        <label>Fecha de realización</label>
        <input type="date" name="fecha_realizacion" value="<?= $tarea['fecha_realizacion'] ?>" class="form-control">
    </div>

    <div class="mb-3">
        <label>Anotaciones antes</label>
        <textarea name="anotaciones_antes" class="form-control"><?= htmlspecialchars($tarea['anotaciones_antes']) ?></textarea>
    </div>

    <div class="mb-3">
        <label>Anotaciones después</label>
        <textarea name="anotaciones_despues" class="form-control"><?= htmlspecialchars($tarea['anotaciones_despues']) ?></textarea>
    </div>

    <div class="mb-3">
        <label>Fichero resumen</label>
        <input type="file" name="resumen" class="form-control">
        <?php if ($tarea['fichero_resumen']): ?>
            <p class="mt-2">Actual: <?= htmlspecialchars($tarea['fichero_resumen']) ?></p>
        <?php endif; ?>
    </div>

    <div class="mb-3">
        <label>Fotos del trabajo</label>
        <input type="file" name="fotos[]" multiple class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Actualizar</button>
</form>

<?php require "views/templates/footer.php"; ?>