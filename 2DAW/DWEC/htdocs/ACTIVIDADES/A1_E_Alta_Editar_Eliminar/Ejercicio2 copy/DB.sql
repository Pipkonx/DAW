-- Crear la base de datos
CREATE DATABASE IF NOT EXISTS e2;

-- Usar la base de datos recién creada
USE e2;

-- Crear la tabla de articulos
CREATE TABLE IF NOT EXISTS articulos (
    codigo VARCHAR(10) PRIMARY KEY,
    descripcion VARCHAR(255) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL
);

-- Insertar algunos datos de ejemplo (opcional)
INSERT INTO articulos (codigo, descripcion, precio) VALUES
('001', 'Teclado', 25.00),
('002', 'Ratón', 15.00),
('003', 'Monitor', 150.00),
('004', 'Auriculares', 30.00),
('005', 'Webcam', 40.00);