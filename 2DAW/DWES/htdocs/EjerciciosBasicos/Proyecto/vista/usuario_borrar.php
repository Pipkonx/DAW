<h2>🗑️ Borrar usuario</h2>

<p>¿Seguro que deseas eliminar este usuario?</p>

<ul>
    <li><strong>ID:</strong> <?= $datos['id'] ?></li>
    <li><strong>Nombre:</strong> <?= $datos['nombre'] ?></li>
    <li><strong>Email:</strong> <?= $datos['email'] ?></li>
    <li><strong>NIF:</strong> <?= $datos['nif'] ?></li>
    <li><strong>CP:</strong> <?= $datos['cp'] ?></li>
</ul>

<form method="post" action="?action=borrar&id=<?= $datos['id'] ?>">
    <input type="hidden" name="id" value="<?= $datos['id'] ?>" />
    <button type="submit" class="btn btn-danger">Sí, borrar definitivamente</button>
</form>

<br>
<a href="?action=listar">⬅️ Cancelar y volver</a>
