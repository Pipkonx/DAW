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
        echo var_dump(explode("-", $_POST["fecha"], 1));
        echo (explode("-", $_POST["fecha"], -1));
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
        if (($_POST["curso"] == "")) {
            $errores["curso"] = "Tienes que seleccionar un curso";
        }
        if ($_POST["fecha"] == "" || intval(explode("-", $_POST["fecha"], 1)) >= 2025) {
            $errores["fecha"] = "Debes de poner una fecha válida";
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
            <br>
            <label for="sexo">Sexo: </label>
            <br>
            <input type="radio" name="sexo[]" id="masculino" value="masculino"> <?= estaMarcado("masculino") ?>
            <label for="masculino">Masculino</label>
            <br>
            <input type="radio" name="sexo[]" id="femenino" value="femenino" <?= estaMarcado("femenino") ?>>
            <label for="femenino">Femenino</label>
            <br>
            <label for="curso" value="<?= filter_input(INPUT_POST, 'curso') ?>">Curso: </label>
            <select name="curso" id="curso">
                <option value="1daw">1DAW</option>
                <option value="2daw">2DAW</option>
                <option value="1dam">1DAM</option>
                <option value="2dam">2DAM</option>
                <option value="1asir">1ASIR</option>
                <option value="2asir">2ASIR</option>
            </select>
            <br>
            <label for="fecha" value="<?= filter_input(INPUT_POST, 'fecha') ?>">Fecha</label>
            <input type="date" name="fecha" id="fecha">
            <br>
            <button type="submit">Enviar</button>
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

    if (isset($_POST["nombre"]) && isset($_POST["apellido"]) && isset($_POST["sexo"]) && isset($_POST["curso"]) && isset($_POST["fecha"])) {
        echo "Nombre : " . $_POST["nombre"] . "<br>Apellido: " . $_POST["apellido"] . "<br>Sexo: " . $_POST["sexo"][0] . "<br>Curso: " . $_POST["curso"] . "<br>Fecha: " . $_POST["fecha"];
    }

    ?>
</body>

</html>