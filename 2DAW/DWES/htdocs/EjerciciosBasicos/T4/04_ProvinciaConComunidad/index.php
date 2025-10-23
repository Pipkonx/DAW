<?php
try {
    $con = new mysqli("localhost", "root", "", "provinciass");
} catch (mysqli_sql_exception $ex) {
    echo $ex->getMessage();
}

// Mostrar las provincias que tiene cada comunidad autónoma, con el siguiente formato: CCAA1 : prov1, prov2. CCAA2: ....

//COMUNIDADES
$sqlCcaa = "SELECT id, nombre FROM tbl_comunidadesautonomas ORDER BY nombre";
$resultadoCcaa = mysqli_query($con, $sqlCcaa);

//PROVINCIAS
$sqlProv = "SELECT id, nombre, comunidad_id FROM tbl_provincias ORDER BY nombre";
$resultadoProv = mysqli_query($con, $sqlProv);

// guardamos las provincias en un array con su id
$provinciasPorComunidad = [];
while ($prov = mysqli_fetch_assoc($resultadoProv)) {
    $provinciasPorComunidad[$prov['comunidad_id']][] = $prov['nombre'];
}

//mostramos
while ($ccaa = mysqli_fetch_assoc($resultadoCcaa)) {
    $id = $ccaa['id'];
    $nombre = $ccaa['nombre'];
    if (isset($provinciasPorComunidad[$id])) {
        echo $nombre . " : " . implode(', ', $provinciasPorComunidad[$id]) . ".<br>";
    }
}


//todo CON SUBCONSULTA
// $sql = "
//     SELECT 
//         ccaa.nombre AS comunidad,
//         GROUP_CONCAT(prov.nombre ORDER BY prov.nombre SEPARATOR ', ') AS provincias
//     FROM tbl_comunidadesautonomas ccaa
//     JOIN tbl_provincias prov ON prov.comunidad_id = ccaa.id
//     GROUP BY ccaa.id, ccaa.nombre
//     ORDER BY ccaa.nombre
// ";

// $resultado = mysqli_query($con, $sql);

// // mysqli_fetch_assoc es para obtener una fila de resultados como un array asociativo.
// while ($fila = mysqli_fetch_assoc($resultado)) {
//     echo $fila['comunidad'] . " : " . $fila['provincias'] . ".<br>";
// }