<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="index.php" method="post">
        <center>
            <h3>DATOS PERSONA</h3>
        </center>
        <br>
        <label for="nombre">Nombre: </label>
        <input type="text" name="nombre" id="nombre">
        <br>
        <label for="apellido">Apellido: </label>
        <input type="text" name="apellido" id="apellido">
        <br>
        <label for="sexo">Sexo: </label>
        <br>
        <label for="masculino">Masculino</label>
        <input type="radio" name="sexo" id="masculino" value="masculino">
        <br>
        <label for="femenino">Femenino</label>
        <input type="radio" name="sexo" id="femenino" value="femenino">
        <br>
        <input type="submit" value="Guardar">
        <br>
        <br>
    </form>

    <?php
    if (isset($_POST["nombre"]) && isset($_POST["apellido"]) && isset($_POST["sexo"])) {
        echo "Nombre : " . $_POST["nombre"] . "<br>Apellido: " . $_POST["apellido"] . "<br>Sexo: " . $_POST["sexo"];
    }
    ?>
</body>

</html>