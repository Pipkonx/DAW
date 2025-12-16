<?php
require_once '/conexion/DB.php';


function obtenerMascotasPorCliente($id_cliente)
{
    $conexion = conectarBD();
    $id_cliente = $conexion->real_escape_string($id_cliente);
    $sql = "SELECT id, nombre, especie, raza, fecha_nacimiento FROM mascotas WHERE id_cliente = $id_cliente";
    $resultado = $conexion->query($sql);
    $mascotas = [];
    if ($resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $mascotas[] = $fila;
        }
    }
    $conexion->close();
    return $mascotas;
}


function obtenerTodosLosClientes()
{
    $conexion = conectarBD();
    $sql = "SELECT id, nombre, apellidos FROM clientes ORDER BY nombre, apellidos";
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

function crearMascota($nombre, $especie, $raza, $fecha_nacimiento, $id_cliente)
{
    $conexion = conectarBD();
    // el real_scape_string es para escapar los caracteres especiales
    $nombre = $conexion->real_escape_string($nombre);
    $especie = $conexion->real_escape_string($especie);
    $raza = $conexion->real_escape_string($raza);
    $fecha_nacimiento = $conexion->real_escape_string($fecha_nacimiento);
    $id_cliente = $conexion->real_escape_string($id_cliente);
    $sql = "INSERT INTO mascotas (nombre, especie, raza, fecha_nacimiento, id_cliente) VALUES ('$nombre', '$especie', '$raza', '$fecha_nacimiento', $id_cliente)";
    $ejecutado = $conexion->query($sql);
    if (!$ejecutado) {
        $error = $conexion->error;
        $conexion->close();
        return ['error' => $error];
    }
    $conexion->close();
    return true;
}

function eliminarMascota($id)
{
    $conexion = conectarBD();
    $stmt = $conexion->prepare("DELETE FROM mascotas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $ejecutado = $stmt->execute();
    $stmt->close();
    $conexion->close();
    return $ejecutado;
}

if ($_GET) {
    if (isset($_GET['cliente_id'])) {
        $id_cliente = $_GET['cliente_id'];
        $mascotas = obtenerMascotasPorCliente($id_cliente);
        echo json_encode($mascotas);
    } elseif (isset($_GET['clientes'])) {
        $clientes = obtenerTodosLosClientes();
        echo json_encode($clientes);
    } else {
        echo json_encode(['error' => 'Parámetros GET inválidos.']);
    }
} elseif ($_POST) {
    $datos = $_POST;
    $action = $datos['_method'] ?? 'POST';
    switch ($action) {
        case 'POST':
            $nombre = $datos['nombre'] ?? null;
            $especie = $datos['especie'] ?? null;
            $raza = $datos['raza'] ?? null;
            $fecha_nacimiento = $datos['fecha_nacimiento'] ?? null;
            $id_cliente = $datos['id_cliente'] ?? null;

            if (empty($nombre) || empty($especie) || empty($id_cliente)) {
                echo json_encode(['error' => 'Nombre, especie y cliente propietario son obligatorios.']);
                exit();
            }

            $resultadoCreacion = crearMascota($nombre, $especie, $raza, $fecha_nacimiento, $id_cliente);

            if (is_array($resultadoCreacion) && isset($resultadoCreacion['error'])) {
                echo json_encode(['error' => 'Error al crear la mascota: ' . $resultadoCreacion['error']]);
            } elseif ($resultadoCreacion === true) {
                echo json_encode(['mensaje' => 'Mascota creada correctamente.']);
            } else {
                echo json_encode(['error' => 'Error desconocido al crear la mascota.']);
            }
            break;

        case 'DELETE':
            $id = $datos['id'] ?? null;
            if (empty($id)) {
                echo json_encode(['error' => 'ID de la mascota es obligatorio para eliminar.']);
                exit();
            }
            if (eliminarMascota($id)) {
                echo json_encode(['mensaje' => 'Mascota eliminada correctamente.']);
            } else {
                echo json_encode(['error' => 'Error al eliminar la mascota.']);
            }
            break;

        default:
            echo json_encode(['error' => 'Acción no permitida.']);
            break;
    }
}
