<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="index.php" method="post">
        <label for="numero">Numero: </label>
        <input type="number" name="numero" id="numero" required>
        <input type="submit" value="Enviar">

        <?php
        if (isset($_POST["numero"])) {
            echo "<br><br>";
            for ($i = 1; $i < 11; $i++) {
                echo $_POST["numero"] . " x " . $i . " = " . $_POST["numero"] * $i . "<br>";
            }
        }
        ?>
</body>

</html>