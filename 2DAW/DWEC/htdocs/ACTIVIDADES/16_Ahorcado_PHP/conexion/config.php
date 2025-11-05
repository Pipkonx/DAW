<?php

class Database {
    private static $instance = null;
    private $conn;

    private $host = 'localhost';
    private $db = 'ahrocado';
    private $user = 'root';
    private $pass = '';

    private function __construct() {
        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db, $this->user, $this->pass);
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
