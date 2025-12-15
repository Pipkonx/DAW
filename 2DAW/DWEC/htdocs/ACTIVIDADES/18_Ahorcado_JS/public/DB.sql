-- Base de datos mínima para el juego del ahorcado (muy simple)

CREATE DATABASE IF NOT EXISTS ahorcado;
USE ahorcado;

-- Tabla única con palabras
DROP TABLE IF EXISTS PALABRAS;
CREATE TABLE PALABRAS (
  id_palabra INT PRIMARY KEY AUTO_INCREMENT,
  texto_palabra VARCHAR(100) NOT NULL
);

-- Palabras de ejemplo
INSERT INTO PALABRAS (texto_palabra) VALUES
('perro'),
('gato'),
('elefante'),
('rojo'),
('azul'),
('verde'),
('manzana'),
('platano'),
('naranja'),
('españa'),
('italia');
