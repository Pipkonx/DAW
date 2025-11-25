<?php

use conexion\configDB;

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        try {
            $config = require __DIR__ . '/../conexion/configDB.php';
            $this->conn = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['db'], $config['user'], $config['pass']);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    //! esto realmente no hace falta para lo que estoy haciendo
    // Opcional: para evitar la clonación
    private function __clone() {}
    // Opcional: para evitar la deserialización
    public function __wakeup() {}
}
