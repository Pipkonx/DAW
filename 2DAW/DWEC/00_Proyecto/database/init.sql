-- Script de inicialización de base de datos para el proyecto de finanzas
-- Ejecutar este script en MySQL/MariaDB antes de usar la aplicación

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS finanzas_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE finanzas_db;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de movimientos financieros
CREATE TABLE IF NOT EXISTS movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo ENUM('ingreso', 'gasto') NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    descripcion TEXT,
    monto DECIMAL(10,2) NOT NULL,
    fecha DATE NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario_fecha (usuario_id, fecha),
    INDEX idx_tipo (tipo),
    INDEX idx_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar datos de ejemplo (opcional)
-- Usuario de prueba: admin@test.com / password: admin123
INSERT IGNORE INTO usuarios (nombre, email, password) VALUES 
('Administrador', 'admin@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Movimientos de ejemplo para el usuario admin
INSERT IGNORE INTO movimientos (usuario_id, tipo, categoria, descripcion, monto, fecha) VALUES 
(1, 'ingreso', 'Salario', 'Salario mensual', 2500.00, '2024-01-01'),
(1, 'gasto', 'Alimentación', 'Compra supermercado', 150.75, '2024-01-02'),
(1, 'gasto', 'Transporte', 'Gasolina', 45.00, '2024-01-03'),
(1, 'ingreso', 'Freelance', 'Proyecto web', 800.00, '2024-01-05'),
(1, 'gasto', 'Entretenimiento', 'Cine', 25.50, '2024-01-06');

-- Mostrar estructura creada
SHOW TABLES;
SELECT 'Base de datos inicializada correctamente' as status;