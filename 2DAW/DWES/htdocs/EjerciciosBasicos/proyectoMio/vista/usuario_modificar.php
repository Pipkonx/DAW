<h2>Modificar Usuario</h2>
<form method="POST">
    <label for="nombre">Nombre: </label>
    <!-- El operador ?? comprueba si el valor de la izquierda esta vacio -->
    <input type="text" name="nombre" value="<?= $datos['nombre'] ?? '' ?>"><br>
    <label for="email">Email: </label>
    <input type="email" name="email" value="<?= $datos['email'] ?? '' ?>"><br>
    <input type="submit" value="Guardar cambios">
</form>
<a href="index.php">Volver</a>