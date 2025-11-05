<?php
require_once __DIR__ . '/../config/Database.php';

class Usuario {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Obtener todos los usuarios
    public function getAll() {
        try {
            $stmt = $this->db->query("SELECT id, nombre, email, password, created_at FROM usuarios ORDER BY id DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
    
    // Obtener usuario por ID
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Obtener usuario por email
    public function getByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Crear nuevo usuario
    public function create($nombre, $email, $password) {
        try {
            // Contraseña sin hash
            $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
            return $stmt->execute([$nombre, $email, $password]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Actualizar usuario
    public function update($id, $nombre, $email, $password) {
        try {
            $stmt = $this->db->prepare("UPDATE usuarios SET nombre = ?, email = ?, password = ? WHERE id = ?");
            return $stmt->execute([$nombre, $email, $password, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Eliminar usuario
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Verificar contraseña (sin hash)
    public function verifyPassword($email, $password) {
        $usuario = $this->getByEmail($email);
        if ($usuario && $usuario['password'] === $password) {
            return $usuario;
        }
        return false;
    }
}