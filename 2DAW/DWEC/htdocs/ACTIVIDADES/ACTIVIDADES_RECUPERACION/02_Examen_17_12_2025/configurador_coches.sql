-- Script SQL completo - Configurador de coches

CREATE TABLE marcas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

CREATE TABLE modelos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_marca INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    precio DECIMAL(10,2) DEFAULT NULL,
    FOREIGN KEY (id_marca) REFERENCES marcas(id)
);

CREATE TABLE compras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    precio_final DECIMAL(10,2) NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO marcas (nombre) VALUES
('Toyota'), ('Ford'), ('Volkswagen'), ('Renault'), ('Seat'),
('BMW'), ('Audi'), ('Mercedes'), ('Peugeot'), ('Hyundai');

INSERT INTO modelos (id_marca, nombre, precio) VALUES
(1, 'Corolla', 22000), (1, 'Yaris', NULL), (1, 'RAV4', 32000),
(2, 'Fiesta', NULL), (2, 'Focus', 24000), (2, 'Kuga', 30000),
(3, 'Golf', 26000), (3, 'Polo', NULL), (3, 'Passat', 31000),
(4, 'Clio', 19000), (4, 'Megane', NULL), (4, 'Captur', 25000),
(5, 'Ibiza', 20000), (5, 'Leon', NULL), (5, 'Arona', 23000),
(6, 'Serie 1', 30000), (6, 'Serie 3', NULL), (6, 'X1', 35000),
(7, 'A1', NULL), (7, 'A3', 29000), (7, 'Q3', 36000),
(8, 'Clase A', 31000), (8, 'Clase C', NULL), (8, 'GLA', 37000),
(9, '208', 19500), (9, '308', NULL), (9, '3008', 28000),
(10, 'i20', NULL), (10, 'i30', 23000), (10, 'Tucson', 29000);
