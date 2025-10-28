<?php
try {
    $con = new mysqli("localhost", "root", "", "provinciass");
} catch (Exception $ex) {
    echo $ex->getMessage();
}

$sql = "SELECT * FROM tbl_provincias";

$resultado = $con->query( $sql);

// mysqli_fetch_assoc es para obtener una fila de resultados como un array asociativo.
$reg = $resultado->fetch_assoc();

while ($$reg = $resultado->fetch_assoc()) {
    echo $reg['cod'] . " " . $reg['nombre'] . " " . $reg['comunidad_id'] . "<br>";

}
