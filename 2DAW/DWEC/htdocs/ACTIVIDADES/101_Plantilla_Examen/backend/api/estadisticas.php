<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// require_once '/conexion/DB.php';
include '/conexion/DB.php';


function obtenerTotalClientes()
{
    $conexion = conectarBD();
    $resultado = $conexion->query("SELECT COUNT(*) as total FROM clientes");
    $fila = $resultado->fetch_assoc();
    $conexion->close();
    return $fila['total'];
}

function obtenerTotalMascotas()
{
    $conexion = conectarBD();
    $resultado = $conexion->query("SELECT COUNT(*) as total FROM mascotas");
    $fila = $resultado->fetch_assoc();
    $conexion->close();
    return $fila['total'];
}

function obtenerEspecieMasComun()
{
    $conexion = conectarBD();
    $resultado = $conexion->query("SELECT especie, COUNT(*) as count FROM mascotas GROUP BY especie ORDER BY count DESC LIMIT 1");
    $fila = $resultado->fetch_assoc();
    $conexion->close();
    return $fila['especie'];
}

function obtenerClienteConMasMascotas()
{
    $conexion = conectarBD();
    $sql = "SELECT c.nombre, c.apellidos, COUNT(m.id) as total_mascotas 
            FROM clientes c 
            JOIN mascotas m ON c.id = m.id_cliente 
            GROUP BY c.id 
            ORDER BY total_mascotas DESC 
            LIMIT 1";
    $resultado = $conexion->query($sql);
    $fila = $resultado->fetch_assoc();
    $conexion->close();
    return $fila['nombre'] . " " . $fila['apellidos'] . " (" . $fila['total_mascotas'] . " mascotas)";
}

$estadisticas = [
    'total_clientes' => obtenerTotalClientes(),
    'total_mascotas' => obtenerTotalMascotas(),
    'especie_mas_comun' => obtenerEspecieMasComun(),
    'cliente_con_mas_mascotas' => obtenerClienteConMasMascotas()
];
echo json_encode($estadisticas);

?>