<?php require "views/templates/header.php"; ?>

<h2>Editar usuario</h2>
<form method="post">
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($user['nombre']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
    </div>
    <div class="mb-3">
        <label>Rol</label>
        <select name="rol" class="form-select">
            <option value="admin" <?= $user['rol'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
            <option value="operador" <?= $user['rol'] == 'operador' ? 'selected' : '' ?>>Operador</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Actualizar</button>
</form>

<?php require "views/templates/footer.php"; ?>