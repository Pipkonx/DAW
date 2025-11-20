CREATE DATABASE IF NOT EXISTS tareasdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE tareasdb;

-- Usuarios
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'operario') NOT NULL DEFAULT 'operario',
    remember_token VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tareas
CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nif_cif VARCHAR(20) NOT NULL,
    contacto_nombre VARCHAR(100) NOT NULL,
    contacto_telefono VARCHAR(30),
    descripcion TEXT NOT NULL,
    correo VARCHAR(150) NOT NULL,
    direccion VARCHAR(255),
    poblacion VARCHAR(100),
    codigo_postal CHAR(5),
    provincia_code SMALLINT,
    estado CHAR(1) DEFAULT 'B', -- B,P,R,C...
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_realizacion DATE,
    operario VARCHAR(100),
    anotaciones_antes TEXT,
    anotaciones_despues TEXT,
    archivo_resumen VARCHAR(255),
    fotos JSON
);

-- Índices útiles
CREATE INDEX idx_tasks_estado ON tasks (estado);

CREATE INDEX idx_tasks_fecha ON tasks (fecha_realizacion);