<?php
$url = 'http://localhost:8000/api/estadisticas.php';
$response = file_get_contents($url);
if ($response === FALSE) {
    echo 'Error al obtener las estadísticas.';
} else {
    echo $response;
}
?>