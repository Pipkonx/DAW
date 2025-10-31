<h2>🗑️ Borrar tarea</h2>

<p>¿Seguro que deseas eliminar esta tarea?</p>

<ul>
    <li><strong>ID:</strong> <?= $datos['id'] ?></li>
    <li><strong>Nombre:</strong> <?= htmlspecialchars($datos['nombreTarea']) ?></li>
    <li><strong>Descripción:</strong> <?= htmlspecialchars($datos['descripcion']) ?></li>
    <li><strong>Estado:</strong> <?= htmlspecialchars($datos['estado']) ?></li>
    <li><strong>Operario:</strong> <?= htmlspecialchars($datos['operario_encargado']) ?></li>
</ul>

<form method="post" action="?action=tareas_borrar&id=<?= $datos['id'] ?>">
    <input type="hidden" name="id" value="<?= $datos['id'] ?>" />
    <button type="submit" class="btn btn-danger">Sí, borrar definitivamente</button>
    
</form>

<br>
<a href="?action=tareas_listar">⬅️ Cancelar y volver</a>