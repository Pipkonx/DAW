<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h2>Modificar Usuario</h2>
    <form method="POST">
        Nombre: <input type="text" name="nombre" value="<?= $datos['nombre'] ?>" required><br>
        Email: <input type="email" name="email" value="<?= $datos['email'] ?>" required><br>
        <input type="submit" value="Actualizar">
    </form>
    <a href="index.php">Volver</a>

</body>

</html>