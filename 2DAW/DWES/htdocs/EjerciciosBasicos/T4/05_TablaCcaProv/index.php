<?php
try {
    $con = new mysqli("localhost", "root", "", "provinciass");
} catch (mysqli_sql_exception $ex) {
    echo $ex->getMessage();
}

// Realiza una página en PHP que muestre las provincias que tiene cada comunidad
// autónoma, en una tabla con el formato:
// CCAA Provincias
// Andalucía Almería
// Cadiz
// ...
// Sevilla
// Cuando resolvemos problemas siempre es conveniente separar la lógica de negocio
// (obtener los datos de la base de datos, en este caso) de la presentación (mostrar la
// página web). Por este motivo es conveniente que por un lado obtengáis los datos y los
// almacenéis en un array, y por otro generéis el código HTML que muestre los datos

//COMUNIDADES
$sqlCcaa = "SELECT id, nombre FROM tbl_comunidadesautonomas ORDER BY nombre";
$resultadoCcaa = mysqli_query($con, $sqlCcaa);

//PROVINCIAS
$sqlProv = "SELECT cod AS id, nombre, comunidad_id FROM tbl_provincias ORDER BY nombre";
$resultadoProv = mysqli_query($con, $sqlProv);

// guardamos las provincias en un array con su id
$provinciasPorComunidad = [];
while ($prov = mysqli_fetch_assoc($resultadoProv)) {
    //esto hace que se guarde el nombre de la provincia en el array con su id ccaa
    $provinciasPorComunidad[$prov['comunidad_id']][] = $prov['nombre'];
}
echo "<table border='1'>";
echo "<tr><th>CCAA</th><th>Provincias</th></tr>";

//mostramos
while ($ccaa = mysqli_fetch_assoc($resultadoCcaa)) {
    $id = $ccaa['id'];
    echo "<tr><td>" . $ccaa['nombre'] . "</td><td>";
    if (isset($provinciasPorComunidad[$id])) {
        //el implode hace que se convierta el array en una cadena separada por comas
        echo implode('<br>', $provinciasPorComunidad[$id]);
    }
    echo "</td></tr>";
}
echo "</table>";
