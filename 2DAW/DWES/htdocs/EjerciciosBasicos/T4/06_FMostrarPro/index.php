<?php
try {
    $con = new mysqli("localhost", "root", "", "provinciass");
} catch (mysqli_sql_exception $ex) {
    echo $ex->getMessage();
}

// Realiza una página que permita seleccionar la comunidad autónoma en un formulario y nos muestre las provincias que hay en la CCAA seleccionada.

// Después de enviar el formulario se mostrará el nombre de la comunidad autónoma seleccionada y las provincias que la conforman.

// Bloque Modificaciones
// Habitualmente nos estamos conectando desde múltiples páginas a la misma base de  datos en una aplicación, por lo que estaremos incluyendo redundantemente los datos de conexión en cada una de las páginas en las que nos conectamos. Por este motivo puede ser conveniente la creación de un fichero en php que incluya toda la información sobre la conexión

// -- bbdd_conex.inc.php –
// <?php
// Guía Didáctica Desarrollo Web en Entorno Servidor
// Revisión 08/11/21
// $host='localhost';
// $user='root';
// $passwd='';
// $conex=mysql_connect($host, $user, $passwd) or die('Error BBDD');
// -- otro_fichero.php –
// <?php

// include ('bbdd_conex.inc.php');
// // resto del código
// Más adelante se abordará este problema desde un enfoque de POO, lo que
// nos creará soluciones más elegantes


//COMUNIDADES
$sqlCcaa = "SELECT id, nombre FROM tbl_comunidadesautonomas ORDER BY nombre";
$resultadoCcaa = mysqli_query($con, $sqlCcaa);

echo "<form method='post'>";
echo "<label for='ccaa'>Seleccione una Comunidad Autónoma:</label>";
echo "<select name='ccaa' id='ccaa' onchange='this.form.submit()'>";
echo "<option value=''>Selecciona</option>";
while ($ccaa = mysqli_fetch_assoc($resultadoCcaa)) {
    // comprobamos si la ccaa seleccionada es la que mostramos
    if (isset($_POST['ccaa']) && $_POST['ccaa'] == $ccaa['id']) {
        $selected = 'selected';
    } else {
        $selected = '';
    }
    echo "<option value='" .  $ccaa["id"] . "' $selected>" . $ccaa["nombre"] . "</option>";
}
echo "</select>";
echo "</form>";

if ($_POST) {
    $idCcaa = intval($_POST['ccaa']);

    $sqlNomCcaa = "SELECT nombre FROM tbl_comunidadesautonomas WHERE id = $idCcaa";
    $resNomCcaa = mysqli_query($con, $sqlNomCcaa);
    $nomCcaa = mysqli_fetch_assoc($resNomCcaa)['nombre'];

    $sqlProv = "SELECT cod AS id, nombre FROM tbl_provincias WHERE comunidad_id = $idCcaa ORDER BY nombre";
    $resultadoProv = mysqli_query($con, $sqlProv);

    echo "<h2>Comunidad Autónoma: $nomCcaa </h2>";
    echo "<ul>";
    while ($prov = mysqli_fetch_assoc($resultadoProv)) {
        echo "<li>" . $prov['nombre'] . "</li>";
    }
    echo "</ul>";
}
