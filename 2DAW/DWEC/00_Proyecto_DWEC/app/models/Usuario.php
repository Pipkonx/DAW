<?php
require_once __DIR__ . '/../../config/database.php';

class Usuario {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Detecta la columna de contraseña según el esquema presente
    private function getPasswordColumn(): string {
        static $cached;
        if ($cached) return $cached;
        $stmt = $this->db->query('SHOW COLUMNS FROM usuarios');
        $rows = $stmt->fetchAll();
        $cols = array_map(fn($r) => $r['Field'] ?? '', $rows);
        $cached = in_array('password_hash', $cols, true) ? 'password_hash' : 'password';
        return $cached;
    }

    public function registrarUsuario(string $nombre, string $email, string $password): array {
        // Validar si el email ya existe
        $stmt = $this->db->prepare('SELECT id FROM usuarios WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'error' => 'El email ya está registrado'];
        }

        // Guardar la contraseña sin aplicar hash
        $pwdCol = $this->getPasswordColumn();
        $stmt = $this->db->prepare("INSERT INTO usuarios (nombre, email, {$pwdCol}) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $email, $password]);
        $id = (int)$this->db->lastInsertId();

        return ['success' => true, 'id' => $id];
    }

    public function iniciarSesion(string $email, string $password): array {
        $pwdCol = $this->getPasswordColumn();
        $stmt = $this->db->prepare("SELECT id, nombre, email, {$pwdCol} AS pwd FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !isset($user['pwd']) || $password !== $user['pwd']) {
            return ['success' => false, 'error' => 'Credenciales inválidas'];
        }

        return ['success' => true, 'user' => [
            'id' => (int)$user['id'],
            'nombre' => $user['nombre'],
            'email' => $user['email'],
        ]];
    }

    public function obtenerUsuarioPorId(int $id): ?array {
        $stmt = $this->db->prepare('SELECT id, nombre, email, fecha_registro FROM usuarios WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        if (!$user) return null;
        return [
            'id' => (int)$user['id'],
            'nombre' => $user['nombre'],
            'email' => $user['email'],
            'fecha_registro' => $user['fecha_registro'],
        ];
    }
}