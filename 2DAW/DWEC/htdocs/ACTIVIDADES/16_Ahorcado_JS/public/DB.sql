CREATE TABLE JUGADORES (
    id_jugador INT PRIMARY KEY AUTO_INCREMENT,
    login VARCHAR(50) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    es_administrador BOOLEAN DEFAULT 0
);

CREATE TABLE CATEGORIAS (
    id_categoria INT PRIMARY KEY AUTO_INCREMENT,
    nombre_categoria VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE PALABRAS (
    id_palabra INT PRIMARY KEY AUTO_INCREMENT,
    texto_palabra VARCHAR(100) NOT NULL,
    id_categoria INT,
    FOREIGN KEY (id_categoria) REFERENCES CATEGORIAS(id_categoria)
);

CREATE TABLE PARTIDAS (
    id_partida INT PRIMARY KEY AUTO_INCREMENT,
    id_jugador INT,
    id_palabra_jugada INT,
    fecha_partida DATETIME NOT NULL,
    letras_acertadas INT DEFAULT 0,
    letras_falladas INT DEFAULT 0,
    palabra_acertada BOOLEAN,
    puntuacion_obtenida INT,
    FOREIGN KEY (id_jugador) REFERENCES JUGADORES(id_jugador),
    FOREIGN KEY (id_palabra_jugada) REFERENCES PALABRAS(id_palabra)
);


-- Insert admin user (admin / admin)
INSERT INTO JUGADORES (login, contrasena, es_administrador)
VALUES ('admin', 'admin', 1);

-- Insert regular user (usuario / usuario)
INSERT INTO JUGADORES (login, contrasena, es_administrador)
VALUES ('usuario', 'usuario', 0);

-- Insert categories
INSERT INTO CATEGORIAS (nombre_categoria) VALUES
('Animales'),
('Colores'),
('Frutas'),
('Países');

-- Insert words for category 'Animales'
INSERT INTO PALABRAS (texto_palabra, id_categoria)
SELECT 'gato', id_categoria FROM CATEGORIAS WHERE nombre_categoria = 'Animales';
INSERT INTO PALABRAS (texto_palabra, id_categoria)
SELECT 'perro', id_categoria FROM CATEGORIAS WHERE nombre_categoria = 'Animales';
INSERT INTO PALABRAS (texto_palabra, id_categoria)
SELECT 'elefante', id_categoria FROM CATEGORIAS WHERE nombre_categoria = 'Animales';

-- Insert words for category 'Colores'
INSERT INTO PALABRAS (texto_palabra, id_categoria)
SELECT 'rojo', id_categoria FROM CATEGORIAS WHERE nombre_categoria = 'Colores';
INSERT INTO PALABRAS (texto_palabra, id_categoria)
SELECT 'azul', id_categoria FROM CATEGORIAS WHERE nombre_categoria = 'Colores';
INSERT INTO PALABRAS (texto_palabra, id_categoria)
SELECT 'verde', id_categoria FROM CATEGORIAS WHERE nombre_categoria = 'Colores';

-- Insert words for category 'Frutas'
INSERT INTO PALABRAS (texto_palabra, id_categoria)
SELECT 'manzana', id_categoria FROM CATEGORIAS WHERE nombre_categoria = 'Frutas';
INSERT INTO PALABRAS (texto_palabra, id_categoria)
SELECT 'plátano', id_categoria FROM CATEGORIAS WHERE nombre_categoria = 'Frutas';
INSERT INTO PALABRAS (texto_palabra, id_categoria)
SELECT 'naranja', id_categoria FROM CATEGORIAS WHERE nombre_categoria = 'Frutas';

-- Insert words for category 'Países'
INSERT INTO PALABRAS (texto_palabra, id_categoria)
SELECT 'españa', id_categoria FROM CATEGORIAS WHERE nombre_categoria = 'Países';
INSERT INTO PALABRAS (texto_palabra, id_categoria)
SELECT 'francia', id_categoria FROM CATEGORIAS WHERE nombre_categoria = 'Países';
INSERT INTO PALABRAS (texto_palabra, id_categoria)
SELECT 'italia', id_categoria FROM CATEGORIAS WHERE nombre_categoria = 'Países';


-- Insert sample games
INSERT INTO PARTIDAS (id_jugador, id_palabra_jugada, fecha_partida, letras_acertadas, letras_falladas, palabra_acertada, puntuacion_obtenida)
SELECT j.id_jugador, p.id_palabra, NOW(), 4, 2, 1, 100
FROM JUGADORES j, PALABRAS p
WHERE j.login = 'usuario' AND p.texto_palabra = 'gato';

INSERT INTO PARTIDAS (id_jugador, id_palabra_jugada, fecha_partida, letras_acertadas, letras_falladas, palabra_acertada, puntuacion_obtenida)
SELECT j.id_jugador, p.id_palabra, NOW(), 5, 3, 0, 0
FROM JUGADORES j, PALABRAS p
WHERE j.login = 'usuario' AND p.texto_palabra = 'elefante';

INSERT INTO PARTIDAS (id_jugador, id_palabra_jugada, fecha_partida, letras_acertadas, letras_falladas, palabra_acertada, puntuacion_obtenida)
SELECT j.id_jugador, p.id_palabra, NOW(), 3, 0, 1, 150
FROM JUGADORES j, PALABRAS p
WHERE j.login = 'admin' AND p.texto_palabra = 'rojo';
