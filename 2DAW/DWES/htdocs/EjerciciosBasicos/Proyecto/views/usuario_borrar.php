<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div>
        <h2>¿Eliminar este usuario?</h2>
        <section>
            <p>Nombre: <?= $datos['nombre'] ?></p>
            <p>Email: <?= $datos['email'] ?></p>
        </section>
    </div>
    <section id="Eliminar">
        <form method="POST">
            <input type="submit" value="Confirmar borrado">
        </form>
        <a href="index.php">Cancelar</a>
    </section>

</body>

</html>