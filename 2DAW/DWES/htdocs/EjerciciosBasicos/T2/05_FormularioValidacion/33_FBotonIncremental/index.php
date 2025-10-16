<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boton Incremental</title>
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
                <h3>Incrementar Boton</h3>
            </center>
            <br>
            <p>Contador: <?= contador()?></p>
            <br>
            <button type="submit">+1</button>
            <br>
            <br>
        </form>
    <?php
    }

    function contador() {
    }
    ?>
</body>

</html>