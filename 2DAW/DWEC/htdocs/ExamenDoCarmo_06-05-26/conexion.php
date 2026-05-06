<?php
// Parámetros de conexión al servidor MySQL
$host = "localhost"; // Donde está el servidor MySQL (localhost es nuestro propio ordenador en XAMPP)
$db = "rafaelcordero"; // Nombre exacto de la base de datos que creaste en phpMyAdmin
$user = "root";      // Usuario por defecto en XAMPP
$pass = "";          // Contraseña por defecto en XAMPP suele estar vacía

// try...catch se usa para "intentar" hacer algo que puede fallar. Si falla la conexión, en vez de que la página
// explote con un error feo de servidor, el error "cae" en el bloque catch y podemos controlarlo.
try {
    // PDO (PHP Data Objects) es la forma recomendada, moderna y segura de conectar PHP con cualquier base de datos.
    // charset=utf8 evita que las ñ o los acentos que viajen entre PHP y MySQL salgan con símbolos raros.
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    
    // Configura PDO para que, si alguna consulta SQL sale mal (ej: falta una coma), PHP lance un "error grave" (Excepción) en vez de callarse.
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configura PDO para que SIEMPRE devuelva los resultados de la base de datos como un "Array Asociativo"
    // Un Array Asociativo significa que sacas los datos así: $fila['nombre']
    // Esto es vital porque los arrays asociativos son los más fáciles de convertir en JSON.
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (Exception $e) { 
    // Si falla la conexión (ej: no has encendido MySQL en XAMPP o está mal la contraseña), 
    // el código se mata (die) y escupe un mensaje en JSON para que Axios sepa que hubo un fallo.
    die(json_encode(['error' => $e->getMessage()])); 
}
?>