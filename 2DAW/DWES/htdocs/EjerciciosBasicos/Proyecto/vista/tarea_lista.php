<h2>🗒️ Listado de tareas</h2>

<a href="?action=listar">⬅️ Volver a usuarios</a>
<br><br>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Estado</th>
        <th>Fecha tarea</th>
        <th>Última actualización</th>
        <th>Anotaciones anteriores</th>
        <th>Anotaciones posteriores</th>
        <th>Operario</th>
        <th>Acciones</th>
    </tr>

    <?php if (!empty($tareas)): ?>
        <?php foreach ($tareas as $t): ?>
            <tr>
                <td><?= $t['id'] ?></td>
                <td><?= htmlspecialchars($t['nombreTarea']) ?></td>
                <td><?= htmlspecialchars($t['descripcion']) ?></td>
                <td><?= htmlspecialchars($t['estado']) ?></td>
                <td><?= isset($t['fecha_tarea']) ? $t['fecha_tarea'] : '' ?></td>
                <td><?= isset($t['fecha_actualizacion']) ? $t['fecha_actualizacion'] : '' ?></td>
                <td><?= htmlspecialchars($t['anotaciones_anteriores']) ?></td>
                <td><?= htmlspecialchars($t['anotaciones_posteriores']) ?></td>
                <td><?= htmlspecialchars($t['operario_encargado']) ?></td>
                <td>
                    <a href="?action=tareas_modificar&id=<?= $t['id'] ?>">✏️</a> |
                    <a href="?action=tareas_borrar&id=<?= $t['id'] ?>">🗑️</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="10" class="text-center">No hay tareas registradas</td>
        </tr>
    <?php endif; ?>
</table>