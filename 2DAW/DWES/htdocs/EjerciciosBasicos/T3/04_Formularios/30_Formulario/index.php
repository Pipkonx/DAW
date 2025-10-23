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
        <br><br>
        <label for="sexo">Sexo: </label>
        <br>
        <label for="masculino">Masculino</label>
        <input type="radio" name="sexo" id="masculino" value="masculino">
        <br>
        <label for="femenino">Femenino</label>
        <input type="radio" name="sexo" id="femenino" value="femenino">
        <br><br>
        <label for="curso">Curso: </label>
        <select name="curso" id="curso">
            <option value="1">1 DAW</option>
            <option value="2">2 DAW</option>
            <option value="3">1 ASIR</option>
            <option value="4">2 ASIR</option>
            <option value="5">1 DAM</option>
            <option value="6">2 DAM</option>
        </select>
        <br><br>
        <label for="fecha">Fecha nacimiento: </label>
        <input type="date" name="fecha" id="fecha">
        <br><br>
        <input type="submit" value="Guardar">
        <br>
        <br>
    </form>

    <?php
    if (isset($_POST["nombre"]) && isset($_POST["apellido"]) && isset($_POST["sexo"]) && isset($_POST["curso"]) && isset($_POST["fecha"])) {
        echo "Nombre : " . $_POST["nombre"] . "<br>Apellido: " . $_POST["apellido"] . "<br>Sexo: " . $_POST["sexo"] . "<br>Curso: " . $_POST["curso"] . "<br>Fecha nacimiento: " . $_POST["fecha"];
    }
    ?>
</body>

</html>