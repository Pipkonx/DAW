<?php require "views/templates/header.php"; ?>

<h2>Nuevo usuario</h2>
<form method="post" action="index.php?controller=User&action=create">
    <div class="mb-3">
        <label>Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Contraseña</label>
        <input type="password" name="password" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Rol</label>
        <select name="rol" class="form-select">
            <option value="admin">Administrador</option>
            <option value="operador">Operador</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Guardar</button>
</form>

<?php require "views/templates/footer.php"; ?>