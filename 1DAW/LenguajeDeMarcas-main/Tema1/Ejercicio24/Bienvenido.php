<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido</title>
</head>

<body>
    <h1>Bienvenido</h1> <br>
    <p>Tu nombre es: </p><?php echo $_POST["fname"]; ?><br>
    <p>Tu apellido es: </p><?php echo $_POST["sname"]; ?><br>
    <p>Tu DNI es: </p><?php echo $_POST["dni"]; ?>
</body>

</html>
