<?php require "views/templates/header.php"; ?>

<h2>Editar tarea #<?= $tarea['id'] ?></h2>
<?php if (!empty($validationErrors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($validationErrors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label>Persona de contacto</label>
        <input type="text" name="persona_contacto" value="<?= htmlspecialchars($tarea['persona_contacto']) ?>" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Teléfono</label>
        <input type="text" name="telefono" value="<?= htmlspecialchars($tarea['telefono']) ?>" class="form-control">
    </div>

    <div class="mb-3">
        <label>Descripción</label>
        <textarea name="descripcion" class="form-control" required><?= htmlspecialchars($tarea['descripcion']) ?></textarea>
    </div>

    <div class="mb-3">
        <label>Correo electrónico</label>
        <input type="email" name="correo" value="<?= htmlspecialchars($tarea['correo']) ?>" class="form-control">
    </div>

    <div class="mb-3">
        <label>Dirección</label>
        <input type="text" name="direccion" value="<?= htmlspecialchars($tarea['direccion']) ?>" class="form-control">
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label>Población</label>
            <input type="text" name="poblacion" value="<?= htmlspecialchars($tarea['poblacion']) ?>" class="form-control">
        </div>
        <div class="col-md-4 mb-3">
            <label>Código postal</label>
            <input type="text" name="codigo_postal" value="<?= htmlspecialchars($tarea['codigo_postal']) ?>" class="form-control">
        </div>
        <div class="col-md-4 mb-3">
            <label>Provincia</label>
            <select name="provincia" class="form-select">
                <?php
                $provincias = [
                    '01' => 'Álava','02' => 'Albacete','03' => 'Alicante','04' => 'Almería','05' => 'Ávila','06' => 'Badajoz','07' => 'Islas Baleares','08' => 'Barcelona','09' => 'Burgos','10' => 'Cáceres','11' => 'Cádiz','12' => 'Castellón','13' => 'Ciudad Real','14' => 'Córdoba','15' => 'A Coruña','16' => 'Cuenca','17' => 'Girona','18' => 'Granada','19' => 'Guadalajara','20' => 'Guipúzcoa','21' => 'Huelva','22' => 'Huesca','23' => 'Jaén','24' => 'León','25' => 'Lleida','26' => 'La Rioja','27' => 'Lugo','28' => 'Madrid','29' => 'Málaga','30' => 'Murcia','31' => 'Navarra','32' => 'Ourense','33' => 'Asturias','34' => 'Palencia','35' => 'Las Palmas','36' => 'Pontevedra','37' => 'Salamanca','38' => 'Santa Cruz de Tenerife','39' => 'Cantabria','40' => 'Segovia','41' => 'Sevilla','42' => 'Soria','43' => 'Tarragona','44' => 'Teruel','45' => 'Toledo','46' => 'Valencia','47' => 'Valladolid','48' => 'Vizcaya','49' => 'Zamora','50' => 'Zaragoza','51' => 'Ceuta','52' => 'Melilla'
                ];
                foreach ($provincias as $codigo => $nombre) {
                    $selected = ($tarea['provincia'] === $codigo) ? 'selected' : '';
                    echo "<option value='$codigo' $selected>$nombre</option>";
                }
                ?>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Estado</label>
            <select name="estado" class="form-select">
                <option value="B" <?= $tarea['estado'] === 'B' ? 'selected' : '' ?>>Esperando aprobación</option>
                <option value="P" <?= $tarea['estado'] === 'P' ? 'selected' : '' ?>>Pendiente</option>
                <option value="R" <?= $tarea['estado'] === 'R' ? 'selected' : '' ?>>Realizada</option>
                <option value="C" <?= $tarea['estado'] === 'C' ? 'selected' : '' ?>>Cancelada</option>
            </select>
        </div>
        <div class="col-md-6 mb-3">
            <label>Fecha de realización</label>
            <input type="date" name="fecha_realizacion" value="<?= $tarea['fecha_realizacion'] ?>" class="form-control">
        </div>
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
    <a href="index.php?controller=Task&action=list&role=<?= $_GET['role'] ?? 'operador' ?>&user=<?= $_GET['user'] ?? '' ?>" class="btn btn-secondary ms-2">Cancelar</a>
</form>

<?php require "views/templates/footer.php"; ?>