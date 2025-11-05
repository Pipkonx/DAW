<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Tareas</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Tareas</h1>
        
        <div class="menu">
            <a href="index.php?controller=tarea&action=crear" class="btn btn-primary">Nueva Tarea</a>
            <a href="index.php?controller=usuario&action=index" class="btn btn-secondary">Ver Usuarios</a>
        </div>
        
        <div class="filtros">
            <form method="GET" class="form-inline">
                <input type="hidden" name="controller" value="tarea">
                <input type="hidden" name="action" value="index">
                
                <select name="usuario_id">
                    <option value="">Todos los usuarios</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= $usuario['id'] ?>" <?= $usuario['id'] == $filtroUsuario ? 'selected' : '' ?>>
                            <?= htmlspecialchars($usuario['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <select name="completada">
                    <option value="">Todas</option>
                    <option value="0" <?= $filtroCompletada === '0' ? 'selected' : '' ?>>Pendientes</option>
                    <option value="1" <?= $filtroCompletada === '1' ? 'selected' : '' ?>>Completadas</option>
                </select>
                
                <button type="submit" class="btn btn-sm btn-info">Filtrar</button>
            </form>
        </div>
        
        <?php if (empty($tareas)): ?>
            <div class="alert alert-info">
                No hay tareas registradas.
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Usuario</th>
                        <th>Estado</th>
                        <th>Fecha de Creación</th>
                        <th>Fecha de Completado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tareas as $tarea): ?>
                        <tr class="<?= $tarea['completada'] ? 'completada' : 'pendiente' ?>">
                            <td><?= htmlspecialchars($tarea['id']) ?></td>
                            <td><?= htmlspecialchars($tarea['titulo']) ?></td>
                            <td><?= htmlspecialchars($tarea['descripcion']) ?></td>
                            <td><?= htmlspecialchars($tarea['nombre_usuario']) ?></td>
                            <td>
                                <span class="estado <?= $tarea['completada'] ? 'completada' : 'pendiente' ?>">
                                    <?= $tarea['completada'] ? 'Completada' : 'Pendiente' ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($tarea['fecha_creacion']) ?></td>
                            <td><?= htmlspecialchars($tarea['fecha_completado'] ?? 'N/A') ?></td>
                            <td>
                                <a href="index.php?controller=tarea&action=editar&id=<?= $tarea['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                
                                <?php if ($tarea['completada']): ?>
                                    <a href="index.php?controller=tarea&action=marcarPendiente&id=<?= $tarea['id'] ?>" class="btn btn-sm btn-info">Marcar Pendiente</a>
                                <?php else: ?>
                                    <a href="index.php?controller=tarea&action=marcarCompletada&id=<?= $tarea['id'] ?>" class="btn btn-sm btn-success">Marcar Completada</a>
                                <?php endif; ?>
                                
                                <a href="index.php?controller=tarea&action=eliminar&id=<?= $tarea['id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('¿Está seguro de eliminar esta tarea?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>