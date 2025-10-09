<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    $errores = [];
    if ($_POST) {
        echo "<h1>Formulario enviado</h1>";
        if ($_POST["nombre"] == "") {
            $errores["nombre"] = "Falta el nombre";
        }
        if ($_POST["apellido"] == "") {
            $errores["apellido"] = "Falta el apellido";
        }
        if (!isset($_POST["sexo"])) {
            $errores["sexo"] = "Hay que seleccionar un sexo";
        }
        if ($_POST["observaciones"] == "") {
            $errores["observaciones"] = "Debe de agregar observaciones";
        }
        if ($errores) {
            echo '<div style="color:red">';
            foreach ($errores as $error) {
                echo "<p>$error</p>";
            }
            echo "</div>";
            formulario();
        } else {
            echo "<p>Campos recibidos:";
            print_r($_POST);
        }
    } else {
        formulario();
    }

    function formulario()
    {
    ?>

        <form method="post">
            <center>
                <h3>DATOS PERSONA</h3>
            </center>
            <br>
            <label for="nombre" value="<?= filter_input(INPUT_POST, 'nombre') ?>">Nombre: </label>
            <input type="text" name="nombre" id="nombre">
            <br>
            <label for="apellido" value="<?= filter_input(INPUT_POST, 'apellido') ?>">Apellido: </label>
            <input type="text" name="apellido" id="apellido">
            <br><br>
            <label for="sexo">Sexo: </label>
            <br>
            <input type="radio" name="sexo[]" id="masculino" value="masculino"> <?= estaMarcado("masculino") ?>
            <label for="masculino">Masculino</label>
            <br>
            <input type="radio" name="sexo[]" id="femenino" value="femenino" <?= estaMarcado("femenino") ?>>
            <label for="femenino">Femenino</label>
            <br><br>
            <label for="observaciones">Observaciones</label><br>
            <textarea name="observaciones" id="observaciones" cols="50" rows="5"></textarea>
            <br><br>
            <button type="submit">Enviar</button>
            <br>
            <br>
        </form>
    <?php
    }

    function estaMarcado(string $value)
    {
        $opcionesMarcadas = isset($_POST['sexo']) ? $_POST["sexo"] : [];
        if (in_array($value, $opcionesMarcadas)) {
            return "checked";
        }
        return "";
    }

    if (isset($_POST["nombre"]) && isset($_POST["apellido"]) && isset($_POST["sexo"])) {
        echo "Nombre : " . $_POST["nombre"] . "<br>Apellido: " . $_POST["apellido"] . "<br>Sexo: " . $_POST["sexo"][0];
    }


    if (isset($_POST["observaciones"])) {
        $_POST["observaciones"] = str_replace("\n", "<br>", $_POST["observaciones"]);
    }
    if (isset($_POST["observaciones"])) {
        $_POST["observaciones"] = nl2br($_POST["observaciones"]);
    }
    ?>
</body>

</html>