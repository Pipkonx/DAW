<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="index.php" method="post">
        <label for="nombre">Nombre: </label>
        <input type="text" name="nombre" id="nombre">
        <label for="apellido">Apellido: </label>
        <input type="text" name="apellido" id="apellido">
        <input type="submit" value="Mostrar">
    </form>
    <?php
    if (isset($_POST["nombre"]) && isset($_POST["apellido"])) {
        echo "<br>Nombre : " . $_POST["nombre"] . "<br>Apellido: " . $_POST["apellido"];
    }
    ?>
</body>

</html>