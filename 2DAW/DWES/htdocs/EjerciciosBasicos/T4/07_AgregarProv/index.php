<?php

//Realiza una página que nos permita añadir nuevas provincias a nuestra base de datos. Las provincias creadas estarán ubicadas en una nueva comunidad autónoma que se llamará “Nuevas provincias”.

// La entrada será filtrada, de forma que no se permitirá que introduzcan provincias repetidas o que se introduzcan cadenas en blanco. Igualmente el nombre de la provincia no deberá contener ningún dígito. Véase el artículo “Expresiones regulares en PHP” o el o el minimanual Explicaciones y ejemplos para el manejo de expresiones regulares.

// El programa tendrá un formulario similar al siguiente:
// Provincia: [Añadir] Ver todas las provincias
// Después de cambiar, filtrando que los datos sean correctos, se seguirá preguntando para seguir cambiando el nombre.
// Donde desde el enlace “Ver todas las provincias” mostraremos todas las provincias y
// comunidades autónomas que tenemos almacenadas

try {
    $con = new mysqli("localhost", "root", "", "provinciass");
} catch (mysqli_sql_exception $ex) {
    echo $ex->getMessage();
}

if (!isset($_POST['ccaa'])) {
    echo "<h1>Agregar Provincia</h1>";
    echo "<form method='post'>";
    echo "<label for='ccaa'>Comunidad autonoma:</label>";
    echo "<input type='text' name='ccaa' id='ccaa'>";
    echo "<button type='submit' onclick='this.form.submit()'>Añadir</button>";
    echo "</form>";
}
if (isset($_POST['ccaa'])) {
    echo "<h1>Funciona</h1>";
    $cod = "SELECT count(*) from tbl_provincias";

    $consulta = "INSERT INTO tbl_provincias (cod, nombre, comunidad_id) VALUES ('$cod', '$_POST[ccaa]', 'null'); ";
    $result = mysqli_query($con,$consulta);
    if ($result) {
        echo "<h2>Provincia añadida" . $_POST['ccaa'] . "</h2>";
    } else {
        echo "<h2>Error al añadir la provincia</h2>";
    }
}
