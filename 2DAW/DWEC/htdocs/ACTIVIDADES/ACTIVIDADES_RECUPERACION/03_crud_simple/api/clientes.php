<?php
function conectar()
{
    $servidor = "localhost";
    $usuario = "root";
    $password = "";
    $base_datos = "veterinaria";

    $conexion = new mysqli($servidor, $usuario, $password, $base_datos);

    if ($conexion->connect_error) {
        echo json_encode(['error' => 'Conexión fallida: ' . $conexion->connect_error]);
        exit();
    }
    return $conexion;
}

function obtener($busqueda = null)
{
    $conexion = conectar();
    $sql = "SELECT * FROM clientes ORDER BY id ASC";

    $resultado = $conexion->query($sql);
    $clientes = [];

    if ($resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $clientes[] = $fila;
        }
    }
    $conexion->close();
    return $clientes;
}

function obtenerClientePorId($id)
{
    $conexion = conectar();
    $sql = "SELECT * FROM clientes WHERE id = $id";
    $resultado = $conexion->query($sql);
    $cliente = null;
    if ($resultado->num_rows > 0) {
        $cliente = $resultado->fetch_assoc();
    }
    $conexion->close();
    return $cliente;
}

function agregar($nombre, $apellidos, $telefono, $email, $direccion)
{
    $conexion = conectar();
    $sql = "INSERT INTO clientes (nombre, apellidos, telefono, email, direccion, fecha_alta) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssss", $nombre, $apellidos, $telefono, $email, $direccion);
    $resultado = $stmt->execute();
    $stmt->close();
    $conexion->close();
    return $resultado;
}

function actualizarCliente($id, $nombre, $apellidos, $telefono, $email, $direccion)
{
    $conexion = conectar();
    $sql = "UPDATE clientes SET nombre = '$nombre', apellidos = '$apellidos', telefono = '$telefono', email = '$email', direccion = '$direccion' WHERE id = $id";
    $resultado = $conexion->query($sql);
    $conexion->close();
    return $resultado;
}

function eliminarCliente($id)
{
    $conexion = conectar();
    $sql = "DELETE FROM clientes WHERE id = $id";
    $resultado = $conexion->query($sql);
    $conexion->close();
    return $resultado;
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $nombre = $data['nombre'] ?? '';
    $apellidos = $data['apellidos'] ?? '';
    $telefono = $data['telefono'] ?? '';
    $email = $data['email'] ?? '';
    $direccion = $data['direccion'] ?? '';

    if (!empty($nombre) && !empty($apellidos) && !empty($telefono) && !empty($email) && !empty($direccion)) {
        if (agregar($nombre, $apellidos, $telefono, $email, $direccion)) {
            echo json_encode(['success' => true, 'message' => 'Cliente agregado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al agregar cliente.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Faltan datos para agregar el cliente.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $cliente = obtenerClientePorId($id);
    if ($cliente) {
        echo json_encode($cliente);
    } else {
        echo json_encode(['success' => false, 'message' => 'Cliente no encontrado.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? '';
    $nombre = $data['nombre'] ?? '';
    $apellidos = $data['apellidos'] ?? '';
    $telefono = $data['telefono'] ?? '';
    $email = $data['email'] ?? '';
    $direccion = $data['direccion'] ?? '';

    if (!empty($id) && !empty($nombre) && !empty($apellidos) && !empty($telefono) && !empty($email) && !empty($direccion)) {
        if (actualizarCliente($id, $nombre, $apellidos, $telefono, $email, $direccion)) {
            echo json_encode(['success' => true, 'message' => 'Cliente actualizado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar cliente.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Faltan datos para actualizar el cliente.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? '';

    if (!empty($id)) {
        if (eliminarCliente($id)) {
            echo json_encode(['success' => true, 'message' => 'Cliente eliminado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar cliente.']);
        }
    }
} else {
    echo json_encode(obtener());
}