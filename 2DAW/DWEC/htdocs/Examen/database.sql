-- ════════════════════════════════════════════
-- database.sql — Ejemplo Frutería (Maestro-Detalle)
-- ════════════════════════════════════════════

-- 1. Crear la Base de Datos
CREATE DATABASE IF NOT EXISTS fruteria_db;
USE fruteria_db;

-- 2. Tabla MAESTRA: Categorías (Secciones de la frutería)
CREATE TABLE IF NOT EXISTS categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- Insertar datos de ejemplo
INSERT INTO categorias (nombre) VALUES ('Frutas'), ('Verduras'), ('Frutos Secos'), ('Legumbres');

-- 3. Tabla DETALLE: Productos (Artículos específicos vinculados a una categoría)
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10, 2) DEFAULT 0.00,
    stock INT DEFAULT 0,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
);

-- Insertar productos de ejemplo vinculados a sus categorías
INSERT INTO productos (categoria_id, nombre, precio, stock) VALUES 
(1, 'Plátano de Canarias', 1.99, 50),  -- Categoría 1: Frutas
(1, 'Naranja de Valencia', 1.50, 100),
(1, 'Manzana Golden', 2.10, 40),
(2, 'Patatas Kennebec', 0.95, 200),     -- Categoría 2: Verduras
(2, 'Lechuga Iceberg', 1.20, 30),
(2, 'Tomate de Ensalada', 2.50, 60),
(3, 'Nueces con cáscara', 4.50, 20),    -- Categoría 3: Frutos Secos
(4, 'Lenteja Pardina', 3.20, 15);       -- Categoría 4: Legumbres
