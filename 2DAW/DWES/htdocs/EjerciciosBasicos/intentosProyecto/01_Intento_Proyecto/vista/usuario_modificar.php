<h2>✏️ Modificar usuario</h2>

<form method="post">
    <label for="nombre">Nombre:</label><br>
    <input type="text" name="nombre" id="nombre" value="<?= $datos['nombre'] ?>" required><br><br>

    <label for="email">Email:</label><br>
    <input type="email" name="email" id="email" value="<?= $datos['email'] ?>" required><br><br>

    <label for="nif">NIF:</label><br>
    <input type="text" name="nif" id="nif" value="<?= $datos['nif'] ?>" required><br><br>

    <label for="cp">Código Postal:</label><br>
    <input type="text" name="cp" id="cp" value="<?= $datos['cp'] ?>" required><br><br>

    <button type="submit">💾</button>
</form>

<br>
<a href="?action=listar">⬅️ Volver</a>
