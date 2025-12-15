<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('titulo'); ?></title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <?php if(!isset($isLoginPage) || !$isLoginPage): ?>
    <nav class="nav">
        <a href="/EjerciciosBasicos/SERVIDOR/admin/tareas">Tareas</a>
        <a href="/EjerciciosBasicos/SERVIDOR/admin/usuarios">Usuarios</a>
        <a href="/EjerciciosBasicos/SERVIDOR/logout">Cerrar Sesión</a>
    </nav>
    <?php endif; ?>

    <div class="container">
        <?php echo $__env->yieldContent('cuerpo'); ?>
    </div>
</body>
</html>
<?php /**PATH C:\Users\Pipkon\Desktop\DAW\2DAW\DWES\htdocs\EjerciciosBasicos\SERVIDOR\resources\views/plantillas/plantilla.blade.php ENDPATH**/ ?>