<h2>✏️ Modificar tarea</h2>

<form method="post">
    <label for="nombreTarea">Nombre de la tarea:</label><br>
    <input type="text" name="nombreTarea" id="nombreTarea" value="<?= htmlspecialchars($datos['nombreTarea']) ?>" required><br><br>

    <label for="descripcion">Descripción:</label><br>
    <input type="text" name="descripcion" id="descripcion" value="<?= htmlspecialchars($datos['descripcion']) ?>" required><br><br>

    <label for="estado">Estado:</label><br>
    <select name="estado" id="estado">
        <option value="B" <?= ($datos['estado'] === 'B') ? 'selected' : '' ?>>Esperando ser aprobado</option>
        <option value="P" <?= ($datos['estado'] === 'P') ? 'selected' : '' ?>>Pendiente</option>
        <option value="R" <?= ($datos['estado'] === 'R') ? 'selected' : '' ?>>Realizada</option>
        <option value="C" <?= ($datos['estado'] === 'C') ? 'selected' : '' ?>>Cancelada</option>
    </select><br><br>

    <label for="anotaciones_anteriores">Anotaciones anteriores:</label><br>
    <input type="text" name="anotaciones_anteriores" id="anotaciones_anteriores" value="<?= htmlspecialchars($datos['anotaciones_anteriores']) ?>"><br><br>

    <label for="anotaciones_posteriores">Anotaciones posteriores:</label><br>
    <input type="text" name="anotaciones_posteriores" id="anotaciones_posteriores" value="<?= htmlspecialchars($datos['anotaciones_posteriores']) ?>"><br><br>

    <label for="operario_encargado">Operario encargado:</label><br>
    <input type="text" name="operario_encargado" id="operario_encargado" value="<?= htmlspecialchars($datos['operario_encargado']) ?>"><br><br>

    <button type="submit">💾 Guardar</button>
</form>

<br>
<a href="?action=tareas_listar">⬅️ Volver</a>