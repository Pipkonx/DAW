<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Gestión de Usuarios</h1>
        
        <div class="menu">
            <a href="index.php?controller=usuario&action=crear" class="btn btn-primary">Nuevo Usuario</a>
            <a href="index.php?controller=tarea&action=index" class="btn btn-secondary">Ver Tareas</a>
        </div>
        
        <?php if (empty($usuarios)): ?>
            <div class="alert alert-info">
                No hay usuarios registrados.
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Contraseña</th>
                        <th>Fecha de Creación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['id']) ?></td>
                            <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td><?= htmlspecialchars($usuario['password']) ?></td>
                            <td><?= htmlspecialchars($usuario['created_at']) ?></td>
                            <td>
                                <a href="index.php?controller=usuario&action=editar&id=<?= $usuario['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                                <a href="index.php?controller=usuario&action=eliminar&id=<?= $usuario['id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('¿Está seguro de eliminar este usuario?')">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>