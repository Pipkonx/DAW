<h2>📋 Listado de usuarios</h2>

<a href="?action=alta">➕ Nuevo usuario</a>
<br><br>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Email</th>
        <th>NIF</th>
        <th>CP</th>
        <th>Acciones</th>
    </tr>

    <?php if (!empty($usuarios)): ?>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= $u['nombre'] ?></td>
                <td><?= $u['email'] ?></td>
                <td><?= $u['nif'] ?></td>
                <td><?= $u['cp'] ?></td>
                <td>
                    <a href="?action=modificar&id=<?= $u['id'] ?>">✏️</a> |
                    <a href="?action=borrar&id=<?= $u['id'] ?>">🗑️</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="6" class="text-center">No hay usuarios registrados</td>
        </tr>
    <?php endif; ?>
</table>
