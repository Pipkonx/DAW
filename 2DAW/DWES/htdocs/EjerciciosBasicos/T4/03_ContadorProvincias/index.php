<?php
try {
    $con = new mysqli("localhost", "root", "", "provinciass");
} catch (mysqli_sql_exception $ex) {
    echo $ex->getMessage();
}

$sql = "SELECT * FROM tbl_provincias";

$resultado = mysqli_query($con, $sql);

// mysqli_fetch_assoc es para obtener una fila de resultados como un array asociativo.
$reg = mysqli_fetch_assoc($resultado);

while ($reg) {
    echo $reg['cod'] . " " . $reg['nombre'] . " " . $reg['comunidad_id'] . "<br>";
    $reg = mysqli_fetch_assoc($resultado);
}
