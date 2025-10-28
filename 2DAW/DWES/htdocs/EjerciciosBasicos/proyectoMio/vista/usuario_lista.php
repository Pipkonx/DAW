<h2>Lista de Usuarios</h2>
<a href="index.php?action=alta">Nuevo Usuario</a>
<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($usuarios as $usuario): ?>
        <tr>
            <td><?= $usuario['id'] ?></td>
            <td><?= $usuario['nombre'] ?></td>
            <td><?= $usuario['email'] ?></td>
            <td>
                <a href="index.php?action=modificar&id=<?= $usuario['id'] ?>">Modificar</a>
                <a href="index.php?action=borrar&id=<?= $usuario['id'] ?>" onclick="return confirm('¿Estás seguro?');">Borrar</a>
            </td>
        </tr>
    <?php endforeach; ?>

</table>