<?php

class conexion
{
    private static $instance = null;
    private $conn;

    private $host = 'localhost';
    private $db_name = 'proyecto';
    private $username = 'root';
    private $password = '';

    // Constructor privado para evitar la instanciación externa
    private function __construct()
    {
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            // Configuración para mostrar errores como excepciones
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Opcional: configurar el modo de fetch por defecto
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // En un entorno de producción, no mostrar el mensaje de error completo
            die("❌ Conexión fallida: " . $e->getMessage());
        }
    }

    // Método estático para obtener la única instancia
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new conexion();
        }
        return self::$instance;
    }

    // Método para obtener la conexión PDO
    public function getConnection()
    {
        return $this->conn;
    }

    // para evitar la clonacion
    public function __clone() {}

    // para evitar la deserialización
    public function __wakeup() {}
}
