-- Crear la base de datos (si no existe)
CREATE DATABASE IF NOT EXISTS tienda_ejemplo;

-- Usar la base de datos
USE tienda_ejemplo;

-- Crear la tabla de categorias
CREATE TABLE IF NOT EXISTS categorias (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL UNIQUE
);

-- Insertar datos de ejemplo en la tabla de categorias
INSERT INTO categorias (nombre_categoria) VALUES
('Electrónica'),
('Ropa'),
('Libros'),
('Hogar');

-- Crear la tabla de productos
CREATE TABLE IF NOT EXISTS productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre_producto VARCHAR(100) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    id_categoria INT,
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
);

-- Insertar datos de ejemplo en la tabla de productos
INSERT INTO productos (nombre_producto, precio, id_categoria) VALUES
('Laptop', 1200.00, (SELECT id_categoria FROM categorias WHERE nombre_categoria = 'Electrónica')),
('Smartphone', 800.00, (SELECT id_categoria FROM categorias WHERE nombre_categoria = 'Electrónica')),
('Camiseta', 25.00, (SELECT id_categoria FROM categorias WHERE nombre_categoria = 'Ropa')),
('Pantalón', 50.00, (SELECT id_categoria FROM categorias WHERE nombre_categoria = 'Ropa')),
('El Señor de los Anillos', 30.00, (SELECT id_categoria FROM categorias WHERE nombre_categoria = 'Libros')),
('La Odisea', 15.00, (SELECT id_categoria FROM categorias WHERE nombre_categoria = 'Libros')),
('Aspiradora', 150.00, (SELECT id_categoria FROM categorias WHERE nombre_categoria = 'Hogar')),
('Juego de sábanas', 40.00, (SELECT id_categoria FROM categorias WHERE nombre_categoria = 'Hogar'));