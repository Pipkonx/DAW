<?php
include_once 'db.php';

$sql = "SELECT * FROM clientes";
$result = $conn->query($sql);

$articulos = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $articulos[] = $row;
    }
}

echo json_encode($articulos);

$conn->close();
?>