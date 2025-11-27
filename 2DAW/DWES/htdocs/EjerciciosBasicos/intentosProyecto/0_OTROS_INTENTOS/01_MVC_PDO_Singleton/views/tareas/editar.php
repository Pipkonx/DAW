<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Tarea</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Editar Tarea</h1>
        
        <div class="menu">
            <a href="index.php?controller=tarea&action=index" class="btn btn-secondary">Volver a Tareas</a>
        </div>
        
        <?php if (!empty($errores)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errores as $error): ?>
                    <p class="error-text"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="form">
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($datos['titulo']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" rows="4" required><?= htmlspecialchars($datos['descripcion']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="usuario_id">Usuario Asignado:</label>
                <select id="usuario_id" name="usuario_id" required>
                    <option value="">Seleccione un usuario</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= $usuario['id'] ?>" <?= $usuario['id'] == $datos['usuario_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($usuario['nombre']) ?> (<?= htmlspecialchars($usuario['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Estado:</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="completada" value="0" <?= !$datos['completada'] ? 'checked' : '' ?>>
                        Pendiente
                    </label>
                    <label>
                        <input type="radio" name="completada" value="1" <?= $datos['completada'] ? 'checked' : '' ?>>
                        Completada
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Actualizar Tarea</button>
                <a href="index.php?controller=tarea&action=index" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>