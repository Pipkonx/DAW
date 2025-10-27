<h2>Modificar Usuario</h2>
<form method="POST">
    Nombre: <input type="text" name="nombre" value="<?= $datos['nombre'] ?>" required><br>
    Email: <input type="email" name="email" value="<?= $datos['email'] ?>" required><br>
    <input type="submit" value="Actualizar">
</form>
<a href="index.php">Volver</a>
