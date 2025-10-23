<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boton Incremental</title>
</head>

<body>
    <?php
    if (!isset($_POST['contador'])) {
        $valor = 0;
    } else {
        $valor = (int) $_POST['contador'] + 1;
    }
    ?>

    <form method="post">
        <center>
            <h3>Incrementar Boton</h3>
            <!-- recordar que tinen que usar campos de formulario con un p no se podr'ia hacer porque no se envia -->
            <input type="text" name="contador" value="<?= $valor ?>">
            <br>
            <br>
            <button type="submit">Sumar</button>
        </center>
    </form>
</body>

</html>