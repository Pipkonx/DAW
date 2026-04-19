-- Script para configurar la base de datos de la actividad
CREATE DATABASE IF NOT EXISTS contact_db;
USE contact_db;

CREATE TABLE IF NOT EXISTS contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(100)
);

-- Datos iniciales de prueba
INSERT INTO contactos (nombre, telefono, email) VALUES 
('Juan Pérez', '600111222', 'juan@example.com'),
('María García', '611222333', 'maria@example.com'),
('Carlos López', '622333444', 'carlos@example.com');
