CREATE DATABASE IF NOT EXISTS poblaciones_db;
USE poblaciones_db;

CREATE TABLE IF NOT EXISTS poblaciones (
    cp VARCHAR(5) PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    habitantes INT
);

INSERT INTO poblaciones (cp, nombre, habitantes) VALUES
('01001', 'Vitoria-Gasteiz', 250000),
('08001', 'Barcelona', 1600000),
('28001', 'Madrid', 3300000),
('41001', 'Sevilla', 690000),
('46001', 'Valencia', 800000),
('50001', 'Zaragoza', 670000),
('29001', 'Málaga', 570000),
('33001', 'Oviedo', 220000),
('15001', 'A Coruña', 240000),
('36001', 'Vigo', 290000);
