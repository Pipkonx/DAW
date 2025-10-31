-- Crear base de datos
CREATE DATABASE IF NOT EXISTS mi_app;
USE mi_app;

-- Tabla usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'operador') NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- Insertar usuarios de ejemplo
INSERT INTO
    usuarios (nombre, email, password, rol)
VALUES (
        'Administrador',
        'admin@miapp.com',
        -- '$2y$10$e0NRjYv3u7k6/u0FmAB5OuwY1oQ9pRZbRKaG3tNNbFQl4kJuMdc9y',
        'admin123',
        'admin'
    ), -- password: admin123
    (
        'Operador',
        'operador@miapp.com',
        -- '$2y$10$6eA3Z4lV6d7YNRk2bEoAe.QKw7vI3Hg6zO9/0KPZVxj9xXydmTXq6',
        'operador123',
        'operador'
    );
-- password: operador123

-- Tabla tareas
CREATE TABLE IF NOT EXISTS tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nif_cif VARCHAR(15) NOT NULL,
    persona_contacto VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    descripcion TEXT NOT NULL,
    correo VARCHAR(100) NOT NULL,
    direccion VARCHAR(200),
    poblacion VARCHAR(50),
    codigo_postal VARCHAR(5),
    provincia VARCHAR(2),
    estado ENUM('B', 'P', 'R', 'C') DEFAULT 'B',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    operario VARCHAR(100),
    fecha_realizacion DATE,
    anotaciones_antes TEXT,
    anotaciones_despues TEXT,
    fichero_resumen VARCHAR(255),
    fotos TEXT
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- Insertar tareas de ejemplo
INSERT INTO
    tareas (
        nif_cif,
        persona_contacto,
        telefono,
        descripcion,
        correo,
        direccion,
        poblacion,
        codigo_postal,
        provincia,
        estado,
        operario,
        fecha_realizacion,
        anotaciones_antes
    )
VALUES (
        '12345678A',
        'Juan Pérez',
        '600123456',
        'Revisión de instalación eléctrica',
        'juanperez@example.com',
        'Calle Falsa 123',
        'Madrid',
        '28001',
        '28',
        'B',
        'Operador',
        '2025-11-05',
        'Revisar cableado y conexiones'
    ),
    (
        'B12345678',
        'María López',
        '611987654',
        'Mantenimiento de aire acondicionado',
        'marialopez@example.com',
        'Avenida Siempre Viva 456',
        'Barcelona',
        '08002',
        '08',
        'P',
        'Operador',
        '2025-11-10',
        'Verificar filtros y compresor'
    );