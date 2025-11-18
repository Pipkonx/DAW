<?php

namespace App\Legacy;

use App\Services\Database;
use PDO;
use PDOException;

class Usuario
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(): array
    {
        try {
            $stmt = $this->db->query("SELECT id, nombre, email, password, created_at FROM usuarios ORDER BY id DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getById(int $id): array|false
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getByEmail(string $email): array|false
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function create(string $nombre, string $email, string $password): bool
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, email, password, created_at) VALUES (?, ?, ?, NOW())");
            return $stmt->execute([$nombre, $email, $password]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update(int $id, string $nombre, string $email, string $password): bool
    {
        try {
            $stmt = $this->db->prepare("UPDATE usuarios SET nombre = ?, email = ?, password = ? WHERE id = ?");
            return $stmt->execute([$nombre, $email, $password, $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function verifyPassword(string $email, string $password): array|false
    {
        $usuario = $this->getByEmail($email);
        if ($usuario && ($usuario['password'] ?? null) === $password) {
            return $usuario;
        }
        return false;
    }
}