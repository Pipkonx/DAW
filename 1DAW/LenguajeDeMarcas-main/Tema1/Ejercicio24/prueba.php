<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario</title>
</head>

<body>
    <h1>Rellena tu CV</h1>

    <form action="Bienvenido.php" method="POST">
        <p>Nombre</p>
        <input type="text" name="fname">

        <p>Apellido</p>
        <input type="text" name="sname">

        <p>Contraseña</p>
        <input type="password" name="password">

        <p>DNI</p>
        <input type="text" name="dni">

        <p>Sexo</p>
        <input type="radio" name="hombre" value="1"> Hombre <br>
        <input type="radio" name="mujer" value="2"> Mujer <br><br>

        <input type="file" src="#" alt="elegir">

        <div class="display">
            <input type="checkbox" name="subscribir" id="#">
            <p>Suscribirme al boletín de novedades</p>
        </div>

        <input type="submit" value="Guardar cambios"></input>
        <input type="reset" value="Borrar los datos introducidos">
    </form>

    <style>
        .display {
            display: flex;
        }
    </style>
</body>

</html>
