<?php require "views/templates/header.php"; ?>
<h2>Usuarios</h2>
<?php if (($_GET['role'] ?? '') === 'admin'): ?>
<a href="index.php?controller=User&action=create&role=admin" class="btn btn-success mb-3">Nuevo usuario</a>
<?php endif; ?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['nombre']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= $u['rol'] ?></td>
                <td>
                    <a href="index.php?controller=User&action=edit&id=<?= $u['id'] ?>" class="btn btn-primary btn-sm">Editar</a>
                    <a href="index.php?controller=User&action=delete&id=<?= $u['id'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require "views/templates/footer.php"; ?>