<?php

//* Usando el modelo Single Stone
class Conexion
{
    private static $instance = null;
    private $conn;
    private function __construct()
    {
        $this->conn = new PDO("mysql:host=localhost;dbname=proyecto;charset=utf8", "root", "");
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Conexion();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conn;
    }
}

// Sin singleStone
// <?php
// $user = "root";
// $pass = "";
// $host = "localhost";
// $dbname = "proyecto";

// try {
//     //todo el proyecto debe estar en PDO
//     $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
//     //set attribute para mostrar la excepcion
//     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// } catch (PDOException $e) {
//     die("❌ Conexión fallida: " . $e->getMessage());
// }
