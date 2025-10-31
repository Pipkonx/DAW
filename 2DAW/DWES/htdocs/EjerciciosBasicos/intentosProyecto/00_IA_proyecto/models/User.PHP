<?php
require_once "config/Database.php";

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM usuarios");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByNombre($nombre)
    {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE nombre = ?");
        $stmt->execute([$nombre]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($nombre, $email, $password, $rol)
    {
        $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
        // return $stmt->execute([$nombre, $email, password_hash($password, PASSWORD_BCRYPT), $rol]);
        return $stmt->execute([$nombre, $email, $password, $rol]);
    }

    public function update($id, $nombre, $email, $rol)
    {
        $stmt = $this->db->prepare("UPDATE usuarios SET nombre=?, email=?, rol=? WHERE id=?");
        return $stmt->execute([$nombre, $email, $rol, $id]);
    }

    public function updateProfile($id, $nombre, $email)
    {
        $stmt = $this->db->prepare("UPDATE usuarios SET nombre=?, email=? WHERE id=?");
        return $stmt->execute([$nombre, $email, $id]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function login($email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email=?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $password === $user['password']) {
            // if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
}
