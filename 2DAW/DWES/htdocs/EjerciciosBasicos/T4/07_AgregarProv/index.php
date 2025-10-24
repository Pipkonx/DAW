<?php

// Realiza una página que nos permita añadir nuevas provincias a nuestra base de datos.
// Las provincias creadas estarán ubicadas en una nueva comunidad autónoma llamada “Nuevas provincias”.
// La entrada será filtrada: no se permiten cadenas en blanco, dígitos en el nombre ni provincias repetidas.

try {
    $con = new mysqli("localhost", "root", "", "provinciass");
} catch (mysqli_sql_exception $ex) {
    echo $ex->getMessage();
    exit;
}

function filtrarNombre($nombre) {
    $nombre = trim($nombre);
    // Sin dígitos, solo letras/espacios (incluye acentos y ñ)
    if ($nombre === '' || preg_match('/\d/', $nombre)) {
        return false;
    }
    if (!preg_match('/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ ]+$/u', $nombre)) {
        return false;
    }
    return $nombre;
}

if (!isset($_POST['ccaa'])) {
    echo "<h1>Agregar Provincia</h1>";
    echo "<form method='post'>";
    echo "<label for='ccaa'>Provincia:</label>";
    echo "<input type='text' name='ccaa' id='ccaa' required>";
    echo "<button type='submit'>Añadir</button>";
    echo "</form>";
}

if (isset($_POST['ccaa'])) {
    $nombreProv = filtrarNombre($_POST['ccaa']);
    if ($nombreProv === false) {
        echo "<h2>Nombre de provincia inválido. No se permiten dígitos ni cadenas vacías.</h2>";
        exit;
    }

    // Comprobar duplicados por nombre
    $stmtCheck = $con->prepare("SELECT 1 FROM tbl_provincias WHERE nombre = ? LIMIT 1");
    $stmtCheck->bind_param('s', $nombreProv);
    $stmtCheck->execute();
    $stmtCheck->store_result();
    if ($stmtCheck->num_rows > 0) {
        echo "<h2>La provincia ya existe: $nombreProv</h2>";
        // mostrar todas las provincias
        $consulta = "SELECT p.cod, p.nombre, c.nombre AS comunidad FROM tbl_provincias p JOIN tbl_comunidadesautonomas c ON p.comunidad_id = c.id ORDER BY p.cod";
        $resultado = mysqli_query($con, $consulta);
        echo "<h3>Listado de provincias</h3>";
        echo "<ul>";
        while ($fila = mysqli_fetch_assoc($resultado)) {
            echo "<li>" . $fila['cod'] . " - " . $fila['nombre'] . " (" . $fila['comunidad'] . ")" . "</li>";
        }
        echo "</ul>";
        exit;
    }
    $stmtCheck->close();

    // Obtener/crear comunidad "Nuevas provincias"
    $comunidadId = null;
    $stmtCcaa = $con->prepare("SELECT id FROM tbl_comunidadesautonomas WHERE nombre = 'Nuevas provincias' LIMIT 1");
    $stmtCcaa->execute();
    $stmtCcaa->bind_result($comunidadId);
    if ($stmtCcaa->fetch()) {
        // existe y ya tenemos $comunidadId
    } else {
        // crear nueva comunidad con id siguiente al máximo
        $stmtCcaa->close();
        $resMax = mysqli_query($con, "SELECT IFNULL(MAX(id),0) AS max_id FROM tbl_comunidadesautonomas");
        $maxId = mysqli_fetch_assoc($resMax)['max_id'];
        $nuevoId = $maxId + 1;
        $stmtInsCcaa = $con->prepare("INSERT INTO tbl_comunidadesautonomas (id, nombre) VALUES (?, 'Nuevas provincias')");
        $stmtInsCcaa->bind_param('i', $nuevoId);
        $stmtInsCcaa->execute();
        $stmtInsCcaa->close();
        $comunidadId = $nuevoId;
    }
    $stmtCcaa->close();

    // Calcular siguiente código disponible (dos dígitos)
    $resCod = mysqli_query($con, "SELECT MAX(CAST(cod AS UNSIGNED)) AS max_cod FROM tbl_provincias");
    $maxCod = (int) mysqli_fetch_assoc($resCod)['max_cod'];
    $nextCodNum = $maxCod + 1;
    if ($nextCodNum > 99) {
        echo "<h2>No hay códigos disponibles (se alcanzó 99).</h2>";
        exit;
    }
    $nextCod = str_pad((string)$nextCodNum, 2, '0', STR_PAD_LEFT);

    // Insertar nueva provincia
    $stmtIns = $con->prepare("INSERT INTO tbl_provincias (cod, nombre, comunidad_id) VALUES (?, ?, ?)");
    $stmtIns->bind_param('ssi', $nextCod, $nombreProv, $comunidadId);
    $stmtIns->execute();
    $stmtIns->close();

    echo "<h2>Provincia añadida: " . htmlspecialchars($nombreProv, ENT_QUOTES, 'UTF-8') . " (cod $nextCod)</h2>";

    // Mostrar todas las provincias con su comunidad
    $consulta = "SELECT p.cod, p.nombre, c.nombre AS comunidad FROM tbl_provincias p JOIN tbl_comunidadesautonomas c ON p.comunidad_id = c.id ORDER BY p.cod";
    $resultado = mysqli_query($con, $consulta);

    echo "<h3>Listado de provincias</h3>";
    echo "<ul>";
    while ($fila = mysqli_fetch_assoc($resultado)) {
        echo "<li>" . $fila['cod'] . " - " . $fila['nombre'] . " (" . $fila['comunidad'] . ")" . "</li>";
    }
    echo "</ul>";
}
