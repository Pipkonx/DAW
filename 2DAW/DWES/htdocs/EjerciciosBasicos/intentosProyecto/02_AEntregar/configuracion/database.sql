-- Base de datos ABC
-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS abc;
USE abc;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de tareas
CREATE TABLE IF NOT EXISTS tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    usuario_id INT NOT NULL,
    completada BOOLEAN DEFAULT FALSE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_completado TIMESTAMP NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar algunos datos de ejemplo
INSERT INTO usuarios (nombre, email, password) VALUES
('Admin', 'admin@abc.com', 'admin123'),
('Usuario1', 'user1@abc.com', 'password1'),
('Usuario2', 'user2@abc.com', 'password2');

-- Insertar tareas de ejemplo
INSERT INTO tareas (titulo, descripcion, usuario_id) VALUES
('Tarea 1', 'Descripción de la tarea 1', 1),
('Tarea 2', 'Descripción de la tarea 2', 1),
('Tarea 3', 'Descripción de la tarea 3', 2);