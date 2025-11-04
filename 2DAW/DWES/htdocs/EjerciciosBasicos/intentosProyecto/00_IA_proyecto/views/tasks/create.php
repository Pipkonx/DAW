<?php require "views/templates/header.php"; ?>

<h2>Crear nueva tarea</h2>

<?php if (!empty($validationErrors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($validationErrors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="index.php?controller=Task&action=create&role=admin">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label>NIF/CIF</label>
            <input type="text" name="nif_cif" class="form-control" value="<?= htmlspecialchars($_POST['nif_cif'] ?? '') ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label>Persona de contacto</label>
            <input type="text" name="persona_contacto" class="form-control" value="<?= htmlspecialchars($_POST['persona_contacto'] ?? '') ?>">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label>Teléfono</label>
            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
        </div>
        <div class="col-md-6 mb-3">
            <label>Correo electrónico</label>
            <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
        </div>
    </div>

    <div class="mb-3">
        <label>Descripción</label>
        <textarea name="descripcion" class="form-control"><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
    </div>

    <div class="mb-3">
        <label>Dirección</label>
        <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($_POST['direccion'] ?? '') ?>">
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label>Población</label>
            <input type="text" name="poblacion" class="form-control" value="<?= htmlspecialchars($_POST['poblacion'] ?? '') ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label>Código postal</label>
            <input type="text" name="codigo_postal" class="form-control" value="<?= htmlspecialchars($_POST['codigo_postal'] ?? '') ?>">
        </div>
        <div class="col-md-4 mb-3">
            <label>Provincia</label>
            <select name="provincia" class="form-select">
                <?php
                $provincias = [
                    '01' => 'Álava',
                    '02' => 'Albacete',
                    '03' => 'Alicante',
                    '04' => 'Almería',
                    '05' => 'Ávila',
                    '06' => 'Badajoz',
                    '07' => 'Islas Baleares',
                    '08' => 'Barcelona',
                    '09' => 'Burgos',
                    '10' => 'Cáceres',
                    '11' => 'Cádiz',
                    '12' => 'Castellón',
                    '13' => 'Ciudad Real',
                    '14' => 'Córdoba',
                    '15' => 'A Coruña',
                    '16' => 'Cuenca',
                    '17' => 'Girona',
                    '18' => 'Granada',
                    '19' => 'Guadalajara',
                    '20' => 'Guipúzcoa',
                    '21' => 'Huelva',
                    '22' => 'Huesca',
                    '23' => 'Jaén',
                    '24' => 'León',
                    '25' => 'Lleida',
                    '26' => 'La Rioja',
                    '27' => 'Lugo',
                    '28' => 'Madrid',
                    '29' => 'Málaga',
                    '30' => 'Murcia',
                    '31' => 'Navarra',
                    '32' => 'Ourense',
                    '33' => 'Asturias',
                    '34' => 'Palencia',
                    '35' => 'Las Palmas',
                    '36' => 'Pontevedra',
                    '37' => 'Salamanca',
                    '38' => 'Santa Cruz de Tenerife',
                    '39' => 'Cantabria',
                    '40' => 'Segovia',
                    '41' => 'Sevilla',
                    '42' => 'Soria',
                    '43' => 'Tarragona',
                    '44' => 'Teruel',
                    '45' => 'Toledo',
                    '46' => 'Valencia',
                    '47' => 'Valladolid',
                    '48' => 'Vizcaya',
                    '49' => 'Zamora',
                    '50' => 'Zaragoza',
                    '51' => 'Ceuta',
                    '52' => 'Melilla'
                ];
                foreach ($provincias as $codigo => $nombre) {
                    $selected = (isset($_POST['provincia']) && $_POST['provincia'] === $codigo) ? 'selected' : '';
                    echo "<option value='$codigo' $selected>$nombre</option>";
                }
                ?>
            </select>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label>Estado</label>
            <select name="estado" class="form-select">
                <?php $estadoSel = $_POST['estado'] ?? 'B'; ?>
                <option value="B" <?= $estadoSel === 'B' ? 'selected' : '' ?>>Esperando aprobación</option>
                <option value="P" <?= $estadoSel === 'P' ? 'selected' : '' ?>>Pendiente</option>
                <option value="R" <?= $estadoSel === 'R' ? 'selected' : '' ?>>Realizada</option>
                <option value="C" <?= $estadoSel === 'C' ? 'selected' : '' ?>>Cancelada</option>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label>Operario encargado</label>
            <select name="operario" class="form-select" required>
                <option value="">Selecciona un operario</option>
                <?php if (!empty($operators)):
                    $sel = $_POST['operario'] ?? '';
                    foreach ($operators as $op):
                        $selected = ($sel === $op['nombre']) ? 'selected' : ''; ?>
                        <option value="<?= htmlspecialchars($op['nombre']) ?>" <?= $selected ?>><?= htmlspecialchars($op['nombre']) ?></option>
                <?php endforeach;
                endif; ?>
            </select>
        </div>
        <div class="col-md-4 mb-3">
            <label>Fecha de realización</label>
            <input type="date" name="fecha_realizacion" class="form-control" value="<?= htmlspecialchars($_POST['fecha_realizacion'] ?? '') ?>">
        </div>
    </div>

    <div class="mb-3">
        <label>Anotaciones anteriores</label>
        <textarea name="anotaciones_antes" class="form-control"><?= htmlspecialchars($_POST['anotaciones_antes'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="index.php?controller=Task&action=list&role=admin" class="btn btn-secondary ms-2">Cancelar</a>
</form>

<?php require "views/templates/footer.php"; ?>