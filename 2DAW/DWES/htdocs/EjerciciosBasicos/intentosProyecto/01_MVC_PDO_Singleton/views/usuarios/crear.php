<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Crear Nuevo Usuario</h1>
        
        <div class="menu">
            <a href="index.php?controller=usuario&action=index" class="btn btn-secondary">Volver a Usuarios</a>
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
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($datos['nombre']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($datos['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
                <small>La contraseña debe tener más de 8 caracteres</small>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Crear Usuario</button>
                <a href="index.php?controller=usuario&action=index" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>