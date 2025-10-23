<?php
$errores = [];

if ($_POST) {
    if ($_POST["nombre"] == "") {
        $errores["nombre"] = "Falta el nombre";
    }

    if (empty($_POST["fecha"])) {
        $errores["fecha"] = "Debes de introducir una fecha";
    } else {
        // con el explode sparamos la fecha en anno mes y lo hacemos una lista
        $partes_fecha = explode("-", $_POST["fecha"]);

        // comprobasmos que son 3 partees
        if (count($partes_fecha) == 3) {
            // le ponmos a las variablese un nombre para identificarlo mejor
            list($year, $month, $day) = $partes_fecha;

            // Usa checkdate() para validar la fecha y no seea mayor que 2025
            if (!checkdate($month, $day, $year)) {
                $errores["fecha"] = "La fecha introducida no es válida.";
            } else {
                if ($year >= 2025) { 
                    $errores["fecha"] = "El año de la fecha no puede ser 2025 o posterior.";
                }
            }
        } else {
            $errores["fecha"] = "El formato de la fecha es incorrecto.";
        }
    }

    if ($errores) {
        echo '<div style="color:red">';
        foreach ($errores as $error) {
            echo "<p>$error</p>";
        }
        echo "</div>";
        formulario();
    } else {
        echo "<p>Campos recibidos:</p>";
        print_r($_POST);
    }
} else {
    formulario();
}

function formulario()
{
?>
    <!-- Tu formulario HTML -->
    <form method="post">
        <center>
            <h3>DATOS PERSONA</h3>
        </center>
        <br>
        <label for="nombre">Nombre: </label>
        <input type="text" name="nombre" id="nombre" value="<?= filter_input(INPUT_POST, 'nombre') ?>">
        <br>
        <label for="apellido">Apellido: </label>
        <input type="text" name="apellido" id="apellido" value="<?= filter_input(INPUT_POST, 'apellido') ?>">
        <br>
        <label for="sexo">Sexo: </label>
        <br>
        <input type="radio" name="sexo" id="masculino" value="masculino" <?= estaMarcado("masculino") ?>>
        <label for="masculino">Masculino</label>
        <br>
        <input type="radio" name="sexo" id="femenino" value="femenino" <?= estaMarcado("femenino") ?>>
        <label for="femenino">Femenino</label>
        <br>
        <label for="curso">Curso: </label>
        <select name="curso" id="curso">
            <option value="1daw" <?= estaSeleccionado('1daw') ?>>1DAW</option>
            <option value="2daw" <?= estaSeleccionado('2daw') ?>>2DAW</option>
            <option value="1dam" <?= estaSeleccionado('1dam') ?>>1DAM</option>
            <option value="2dam" <?= estaSeleccionado('2dam') ?>>2DAM</option>
            <option value="1asir" <?= estaSeleccionado('1asir') ?>>1ASIR</option>
            <option value="2asir" <?= estaSeleccionado('2asir') ?>>2ASIR</option>
        </select>
        <br>
        <label for="fecha">Fecha</label>
        <input type="date" name="fecha" id="fecha" value="<?= filter_input(INPUT_POST, 'fecha') ?>">
        <br>
        <button type="submit">Enviar</button>
    </form>
<?php
}

function estaMarcado(string $value)
{
    if (isset($_POST['sexo']) && $_POST['sexo'] == $value) {
        return "checked";
    }
    return "";
}

function estaSeleccionado(string $value)
{
    if (isset($_POST['curso']) && $_POST['curso'] == $value) {
        return "selected";
    }
    return "";
}

if (isset($_POST["nombre"]) && isset($_POST["apellido"]) && isset($_POST["sexo"]) && isset($_POST["curso"]) && isset($_POST["fecha"])) {
    echo "Nombre : " . $_POST["nombre"] . "<br>Apellido: " . $_POST["apellido"] . "<br>Sexo: " . $_POST["sexo"] . "<br>Curso: " . $_POST["curso"] . "<br>Fecha: " . $_POST["fecha"];
}
?>
</body>

</html>