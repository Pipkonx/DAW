<?php

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