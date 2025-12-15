<?php

use conexion\configDB;

class DB
{
    private static $instance = null;
    private $conexion;

    private function __construct()
    {
        try {
            $configuracion = require __DIR__ . '/../conexion/configDB.php';
            $this->conexion = new PDO(
                'mysql:host=' . $configuracion['host'] . ';dbname=' . $configuracion['db'],
                $configuracion['user'],
                $configuracion['pass']
            );
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new DB();
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->conexion;
    }
}
