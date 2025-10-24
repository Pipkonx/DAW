<?php
const LIMITE_CODIGO = 100;

// conectamos base de datos
$con = mysqli_connect("localhost", "root", "", "provinciass");
if (!$con) {
    die("Error al conectar: " . mysqli_connect_error());
}

// Limpiar y vaciar nombre
function limpiarNombre($nombre)
{
    $nombre = trim($nombre);                       // quitamos espacios
    if ($nombre === '') return false;              // no vacío
    if (preg_match('/\d/', $nombre)) return false; // sin dígitos
    // solo letras, espacios y acentos
    if (!preg_match('/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]+$/u', $nombre)) return false;
    return $nombre;
}

// formulario si no se detecta post
if (!isset($_POST['provincia'])) {
    echo "<h1>Añadir provincia</h1>";
    echo '<form method="post">';
    echo 'Provincia: <input type="text" name="provincia" required>';
    echo ' <button type="submit">Añadir</button>';
    echo '</form>';
    exit; // paramos aquí para no mezclar código
}

$nombre = limpiarNombre($_POST['provincia']);
if ($nombre === false) {
    die("<h2>Nombre inválido: no uses números ni dejes el campo vacío.</h2>");
}

// Comprobar si ya existe la provincia
$sql = "SELECT cod FROM tbl_provincias WHERE nombre = '$nombre' LIMIT 1";
$res = mysqli_query($con, $sql);
if (mysqli_num_rows($res) > 0) {
    die("<h2>La provincia $nombre ya existe.</h2>");
}

// busca comunidad Nuevas provincias si no existe la crea
$sqlCcaa = "SELECT id FROM tbl_comunidadesautonomas WHERE nombre = 'Nuevas provincias' LIMIT 1";
$resCcaa = mysqli_query($con, $sqlCcaa);
if (mysqli_num_rows($resCcaa) > 0) {
    $fila = mysqli_fetch_assoc($resCcaa);
    $idComunidad = $fila['id'];
} else {
    // creamos la  comunidad con el siguiente id libre
    $resMax = mysqli_query($con, "SELECT MAX(id) AS maximo FROM tbl_comunidadesautonomas");
    $maximo = mysqli_fetch_assoc($resMax)['maximo'] ?? 0;
    $nuevoId = $maximo + 1;
    mysqli_query($con, "INSERT INTO tbl_comunidadesautonomas (id, nombre) VALUES ($nuevoId, 'Nuevas provincias')");
    $idComunidad = $nuevoId;
}

// calculamos el siguiente codigo de dos digitos

// el cast sirve para convertir el valor de la columna cod en un número sin signo para poder sumar
$resCod = mysqli_query($con, "SELECT MAX(CAST(cod AS UNSIGNED)) AS maximo FROM tbl_provincias");
$maxCod = (int)(mysqli_fetch_assoc($resCod)['maximo'] ?? 0);
$siguiente = $maxCod + 1;
if ($siguiente > LIMITE_CODIGO) {
    die("<h2>No quedan códigos libres (máx 99).</h2>");
}
$codigo = str_pad($siguiente, 2, '0', STR_PAD_LEFT);

mysqli_query($con, "INSERT INTO tbl_provincias (cod, nombre, comunidad_id) VALUES ('$codigo', '$nombre', $idComunidad)");

echo "<h2>Provincia añadida: $nombre (código $codigo)</h2>";

//mostramos todas
echo "<h3>Listado de provincias</h3><ul>";
$resultado = mysqli_query($con, "SELECT p.cod, p.nombre, c.nombre AS comunidad
                                FROM tbl_provincias p
                                JOIN tbl_comunidadesautonomas c ON p.comunidad_id = c.id
                                ORDER BY p.cod");
while ($fila = mysqli_fetch_assoc($resultado)) {
    echo "<li>{$fila['cod']} - {$fila['nombre']} ({$fila['comunidad']})</li>";
}
echo "</ul>";

mysqli_close($con);
