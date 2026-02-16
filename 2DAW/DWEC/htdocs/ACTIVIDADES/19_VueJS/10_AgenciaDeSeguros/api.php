<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$host = 'localhost';
$db   = 'agencia_seguros_new';
$user = 'root'; // Cambiar si es necesario
$pass = '';     // Cambiar si es necesario
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     echo json_encode(['error' => 'Error de conexión: ' . $e->getMessage()]);
     exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ? AND password = ?");
        $stmt->execute([$data['username'], $data['password']]);
        $user = $stmt->fetch();
        echo json_encode(['success' => (bool)$user, 'user' => $user]);
        break;

    case 'getClients':
        $stmt = $pdo->query("SELECT * FROM clientes");
        echo json_encode($stmt->fetchAll());
        break;

    case 'getPolicies':
        $stmt = $pdo->query("SELECT * FROM polizas");
        echo json_encode($stmt->fetchAll());
        break;

    case 'getPayments':
        $stmt = $pdo->query("SELECT * FROM pagos");
        echo json_encode($stmt->fetchAll());
        break;

    case 'addPayment':
        $data = json_decode(file_get_contents('php://input'), true);
        $stmt = $pdo->prepare("INSERT INTO pagos (poliza_id, fecha, importe) VALUES (?, ?, ?)");
        $success = $stmt->execute([$data['policyId'], $data['fecha'], $data['importe']]);
        echo json_encode(['success' => $success, 'id' => $pdo->lastInsertId()]);
        break;

    case 'deletePayment':
        $id = $_GET['id'] ?? 0;
        $stmt = $pdo->prepare("DELETE FROM pagos WHERE id = ?");
        $success = $stmt->execute([$id]);
        echo json_encode(['success' => $success]);
        break;

    default:
        echo json_encode(['error' => 'Acción no válida']);
        break;
}
?>
