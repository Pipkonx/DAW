<?php
// require incluye la conexión a la base de datos para que la variable $pdo de conexion.php esté disponible aquí
require 'conexion.php';

// $_SERVER['REQUEST_METHOD'] detecta con qué verbo HTTP nos ha llamado Axios (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];

// $_GET['accion'] saca el valor de la URL (ej: api.php?accion=categorias).
// El "??" significa: "Si no mandan ninguna acción por la URL, ponle valor vacío ''"
$accion = $_GET['accion'] ?? '';

// json_decode: Cuando Axios hace un POST o un PUT, envía los datos del formulario (formP) escondidos en el "cuerpo" de la petición.
// php://input lee ese cuerpo tal cual llega (en formato JSON), y json_decode lo convierte en un array de PHP ($input) para que podamos sacar los datos como $input['nombre'].
$input = json_decode(file_get_contents('php://input'), true);

if ($accion === 'categorias') {
    if ($method === 'GET') {
        // $pdo->query ejecuta un SQL directo. Se usa solo cuando no hay variables mezcladas (es decir, no hay riesgo de inyección SQL)
        $stmt = $pdo->query("SELECT * FROM categorias");
        
        // fetchAll() transforma las filas de la base de datos en un array de PHP.
        // json_encode() coge ese array y lo convierte a formato texto (JSON) que es lo que entiende Axios.
        echo json_encode($stmt->fetchAll());
    }
} elseif ($accion === 'productos') {
    if ($method === 'GET') {
        $cat_id = $_GET['cat_id'] ?? 0;
        // prepare() se usa por SEGURIDAD. Cuando usamos variables que vienen del usuario, ponemos un "?" en lugar de concatenar la variable.
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE categoria_id = ?");
        // execute() cambia ese "?" por el valor real de forma segura, evitando que un hacker haga "SQL Injection"
        $stmt->execute([$cat_id]);
        echo json_encode($stmt->fetchAll());
        
    } elseif ($method === 'POST') {
        $cat_id = $_GET['cat_id'] ?? 0;
        // INSERT para crear un nuevo producto. Tiene tres "?", así que execute necesita recibir un array con 3 variables.
        $stmt = $pdo->prepare("INSERT INTO productos (categoria_id, nombre, precio) VALUES (?, ?, ?)");
        $stmt->execute([$cat_id, $input['nombre'], $input['precio'] ?? 0]);
        // Si todo va bien, le decimos a Axios "ok, creado con éxito".
        echo json_encode(['success' => true]);
        
    } elseif ($method === 'PUT') {
        $id = $_GET['id'] ?? 0;
        // UPDATE para editar un producto existente basado en su ID.
        $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, precio = ? WHERE id = ?");
        $stmt->execute([$input['nombre'], $input['precio'] ?? 0, $id]);
        echo json_encode(['success' => true]);
        
    } elseif ($method === 'DELETE') {
        $id = $_GET['id'] ?? 0;
        // DELETE para borrar. Solo necesita la ID por la URL.
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    }
} else {
    // Si no hay ?accion= (Escenario 1 de la guía, un CRUD sin categorías) funciona exactamente igual.
    if ($method === 'GET') {
        $stmt = $pdo->query("SELECT * FROM productos");
        echo json_encode($stmt->fetchAll());
    } elseif ($method === 'POST') {
        $stmt = $pdo->prepare("INSERT INTO productos (nombre, precio) VALUES (?, ?)");
        $stmt->execute([$input['nombre'], $input['precio'] ?? 0]);
        echo json_encode(['success' => true]);
    } elseif ($method === 'PUT') {
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, precio = ? WHERE id = ?");
        $stmt->execute([$input['nombre'], $input['precio'] ?? 0, $id]);
        echo json_encode(['success' => true]);
    } elseif ($method === 'DELETE') {
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    }
}
?>
