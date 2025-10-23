<?php
$errores = [];

if ($_POST) {


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
    <form method="post">
        <center>
            <h3>ENCUESTA SIGMA 3</h3>
        </center>
        <br>
        <label for="sexo">Sexo: </label>
        <br>
        <input type="radio" name="sexo" id="masculino" value="masculino" <?= estaMarcado("masculino") ?>>
        <label for="masculino">Masculino</label>
        <br>
        <input type="radio" name="sexo" id="femenino" value="femenino" <?= estaMarcado("femenino") ?>>
        <label for="femenino">Femenino</label>
        <br><br>
        <label for="curso">Curso: </label>
        <br><br>
        <label for="aficiones">Aficiones</label><br>
        <input type="radio" name="aficiones" value="deporte" <?= estaMarcado("deporte") ?>>
        <label for="aficiones">Deporte</label><br>
        <input type="radio" name="aficiones" value="cine" <?= estaMarcado("cine") ?>>
        <label for="aficiones">Cine</label><br>
        <input type="radio" name="aficiones" value="teatro" <?= estaMarcado("teatro") ?>>
        <label for="aficiones">teatro</label>
        <br><br>
        <label for="estudio">Estudios</label><br>
        <input type="radio" name="estudio" value="eso" <?= estaMarcado("eso") ?>>
        <label for="estudio">ESO</label><br>
        <input type="radio" name="estudio" value="cfgmedio" <?= estaMarcado("cfgmedio") ?>>
        <label for="estudio">CFG Medio</label><br>
        <input type="radio" name="estudio" value="cfgsuperior" <?= estaMarcado("cfgsuperior") ?>>
        <label for="estudio">CFG Superior</label><br>
        <input type="radio" name="estudio" value="grado" <?= estaMarcado("grado") ?>>
        <label for="estudio">Grado</label><br><br>
        <label for="vacaciones">Lugar al que te gustaría ir de vacaciones</label><br>
        <input type="radio" name="vacaciones" value="mediterrano" <?= estaMarcado("mediterraneo") ?>>
        <label for="vacaciones">Mediterraneo</label><br>
        <input type="radio" name="vacaciones" value="caribe" <?= estaMarcado("caribe") ?>>
        <label for="vacaciones">Caribe</label><br>
        <input type="radio" name="vacaciones" value="eeuu" <?= estaMarcado("eeuu") ?>>
        <label for="vacaciones">EEUU</label><br>
        <input type="radio" name="vacaciones" value="centroeu" <?= estaMarcado("centroeu") ?>>
        <label for="vacacionese">Centro eu</label><br>
        <br> <button type="submit">Enviar</button>
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
    if (isset($_POST['aficiones']) && $_POST['aficiones'] == $value) {
        return "selected";
    }
    return "";
    if (isset($_POST['estudio']) && $_POST['estudio'] == $value) {
        return "selected";
    }
    return "";
    if (isset($_POST['vacaciones']) && $_POST['vacaciones'] == $value) {
        return "selected";
    }
    return "";
}

// esto es para que se muestre el valor de las aficiones, estudio y vacaciones
if (isset($_POST["sexo"]) && isset($_POST["curso"]) && isset($_POST["aficiones"]) && isset($_POST["estudio"]) && isset($_POST["vacaciones"])) {
    echo "<br>Sexo: " . $_POST["sexo"] . "<br>Curso: " . $_POST["curso"] . "<br>Aficiones: ". $_POST["aficiones"]. "<br>Estudios: " . $_POST["estudio"] . "<br>Vacaciones: " . $_POST["vacaciones"];
}
?>
</body>

</html>