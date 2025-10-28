<h2>¿Eliminar este usuario?</h2>
<p>Nombre: <?= isset($datos['nombre']) ? $datos['nombre'] : '' ?></p>
<p>Email: <?= isset($datos['email']) ? $datos['email'] : '' ?></p>
<form method="POST">
    <input type="hidden" name="id" value="<?= isset($datos['id']) ? $datos['id'] : '' ?>">
    <input type="submit" value="Confirmar borrado">
</form>
<a href="index.php">Cancelar</a>