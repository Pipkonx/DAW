<?php
// require_once '/conexion/DB.php';
include

function conectarBD()
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
    $conexion = conectarBD();
    $sql = "SELECT id, nombre, apellidos, telefono, email, direccion, fecha_alta FROM clientes";

    if ($busqueda) {

        $sql .= " WHERE nombre LIKE '%" . $busqueda . "%' OR telefono LIKE '%" . $busqueda . "%'";
    }

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

function obtenerPorId($id)
{
    $conexion = conectarBD();
    $sql = "SELECT id, nombre, apellidos, telefono, email, direccion, fecha_alta FROM clientes WHERE id = $id";
    $resultado = $conexion->query($sql);
    $cliente = null;
    if ($resultado->num_rows > 0) {
        $cliente = $resultado->fetch_assoc();
    }
    $conexion->close();
    return $cliente;
}

function guardar($nombre, $apellidos, $telefono, $email = null, $direccion = null)
{
    $conexion = conectarBD();
    $fecha_alta = date('Y-m-d H:i:s');
    $sql = "INSERT INTO clientes (nombre, apellidos, telefono, email, direccion, fecha_alta) VALUES ('$nombre', '$apellidos', '$telefono', '$email', '$direccion', '$fecha_alta')";
    $ejecutado = $conexion->query($sql);
    $conexion->close();
    return $ejecutado;
}

function modificar($id, $nombre, $apellidos, $telefono, $email = null, $direccion = null)
{
    $conexion = conectarBD();

    $sql = "UPDATE clientes SET nombre = '$nombre', apellidos = '$apellidos', telefono = '$telefono', email = '$email', direccion = '$direccion' WHERE id = $id";
    $ejecutado = $conexion->query($sql);
    $conexion->close();
    return $ejecutado;
}

function eliminar($id)
{
    $conexion = conectarBD();
    $conexion->begin_transaction();

    try {
        // Eliminar mascotas asociadas al cliente
        $sqlMascotas = "DELETE FROM mascotas WHERE id_cliente = $id";
        $conexion->query($sqlMascotas);

        // Eliminar cliente
        $sqlCliente = "DELETE FROM clientes WHERE id = $id";
        $ejecutado = $conexion->query($sqlCliente);

        if (!$ejecutado) {
            throw new Exception("Error al eliminar el cliente.");
        }

        $conexion->commit();
        $conexion->close();
        return true;
    } catch (Exception $e) {
        $conexion->rollback();
        $conexion->close();
        error_log($e->getMessage()); // Registrar el error para depuración
        return false;
    }
}


if ($_GET) {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $cliente = obtenerPorId($id);
        echo json_encode($cliente);
    } else {
        $busqueda = $_GET['busqueda'] ?? null;
        $clientes = obtener($busqueda);
        echo json_encode($clientes);
    }
} elseif ($_POST) {
    $datos = $_POST;

    $action = $datos['_method'] ?? 'POST';

    switch ($action) {
        case 'POST':
            $nombre = $datos['nombre'] ?? '';
            $apellidos = $datos['apellidos'] ?? '';
            $telefono = $datos['telefono'] ?? '';
            $email = $datos['email'] ?? null;
            $direccion = $datos['direccion'] ?? null;

            if (empty($nombre) || empty($apellidos) || empty($telefono)) {
                echo json_encode(['mensaje' => 'Faltan datos obligatorios.']);
            }

            $resultadoGuardar = guardar($nombre, $apellidos, $telefono, $email, $direccion);

            if ($resultadoGuardar === true) {
                echo json_encode(['mensaje' => 'Cliente creado exitosamente.']);
            } else {
                echo json_encode(['mensaje' => 'Error al crear el cliente.']);
            }
            break;

        case 'PUT':
            $id = $datos['id'] ?? null;
            $nombre = $datos['nombre'] ?? '';
            $apellidos = $datos['apellidos'] ?? '';
            $telefono = $datos['telefono'] ?? '';
            $email = $datos['email'] ?? null;
            $direccion = $datos['direccion'] ?? null;

            if (empty($id) || empty($nombre) || empty($apellidos) || empty($telefono)) {
                echo json_encode(['mensaje' => 'Faltan datos obligatorios para modificar.']);
            }

            $resultadoModificar = modificar($id, $nombre, $apellidos, $telefono, $email, $direccion);

            if ($resultadoModificar === true) {
                echo json_encode(['mensaje' => 'Cliente modificado exitosamente.']);
            } else {
                echo json_encode(['mensaje' => 'Error al modificar el cliente.']);
            }
            break;

        case 'DELETE':
            $id = $datos['id'] ?? null;

            if (empty($id)) {
                echo json_encode(['mensaje' => 'ID del cliente es obligatorio para eliminar.']);
            }

            if (eliminar($id)) {
                echo json_encode(['mensaje' => 'Cliente eliminado exitosamente.']);
            } else {
                echo json_encode(['mensaje' => 'Error al eliminar el cliente.']);
            }
            break;

        default:
            echo json_encode(['mensaje' => 'Acción no permitida.']);
            break;
    }
} else {
    $clientes = obtener();
    echo json_encode($clientes);
}