<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h2>Alta de Usuario</h2>
    <form method="POST">
        <label for="nombre">Nombre: </label>
        <input type="text" name="nombre" placeholder="Pepe" required><br>
        <label for="email">Email: </label>
        <input type="email" name="email" placeholder="pepe@gmail.com" required><br>
        <input type="submit" value="Guardar" class="button">
    </form>
    <a href="index.php">Volver</a>

</body>

</html>