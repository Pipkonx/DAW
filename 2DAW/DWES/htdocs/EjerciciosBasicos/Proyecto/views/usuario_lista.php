<h2>Lista de Usuarios</h2>
<a href="index.php?action=alta">Nuevo Usuario</a>
<table border="1" cellpadding="5">
<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Acciones</th></tr>
<?php foreach ($datos as $fila): ?>
<tr>
    <td><?= $fila['id'] ?></td>
    <td><?= $fila['nombre'] ?></td>
    <td><?= $fila['email'] ?></td>
    <td>
        <a href="index.php?action=modificar&id=<?= $fila['id'] ?>">Editar</a> |
        <a href="index.php?action=borrar&id=<?= $fila['id'] ?>">Borrar</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
