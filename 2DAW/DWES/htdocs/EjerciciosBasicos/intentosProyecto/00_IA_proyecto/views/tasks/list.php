<?php require "views/templates/header.php"; ?>
<h2>Listado de tareas</h2>
<?php if (!empty($currentUser)): ?>
    <a href="index.php?controller=User&action=edit&id=<?= $currentUser['id'] ?>&role=<?= htmlspecialchars($_GET['role'] ?? 'operador') ?>&user=<?= htmlspecialchars($_GET['user'] ?? '') ?>" class="btn btn-outline-secondary mb-3">Editar perfil</a>
<?php endif; ?>
<?php if ($_GET['role'] === 'admin'): ?>
    <a href="index.php?controller=Task&action=create&role=admin" class="btn btn-success mb-3">Nueva tarea</a>
<?php endif; ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Descripción</th>
            <th>Operario</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tareas as $t): ?>
            <tr>
                <td><?= $t['id'] ?></td>
                <td><?= htmlspecialchars($t['descripcion']) ?></td>
                <td><?= htmlspecialchars($t['operario']) ?></td>
                <td><?= $t['estado'] ?></td>
                <td>
                    <a href="index.php?controller=Task&action=edit&id=<?= $t['id'] ?>&role=<?= $_GET['role'] ?>&user=<?= $_GET['user'] ?>" class="btn btn-primary btn-sm">Editar</a>
                    <?php if ($_GET['role'] === 'admin'): ?>
                        <a href="index.php?controller=Task&action=delete&id=<?= $t['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar esta tarea?');">Eliminar</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php require "views/templates/footer.php"; ?>