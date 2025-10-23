<?php
try {
    $con = new mysqli("localhost", "root", "", "provinciass");
} catch (mysqli_sql_exception $ex) {
    echo $ex->getMessage();
}

$sql = "SELECT * FROM tbl_provincias";

$resultado = mysqli_query($con, $sql);

// mysqli_fetch_assoc es para obtener una fila de resultados como un array asociativo.
while ($fila = mysqli_fetch_assoc($resultado)) {
    echo $fila['cod'] . " " . $fila['nombre'] . " " . $fila['comunidad_id'] . "<br>";
}
