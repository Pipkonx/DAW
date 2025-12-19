<?php

function conectarBD()
{
    $servidor = "localhost";
    $usuario = "root";
    $password = "";
    $base_datos = "veterinaria";

    $conexion = new mysqli($servidor, $usuario, $password, $base_datos);
    return $conexion;
}

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

?>