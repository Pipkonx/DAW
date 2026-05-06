CREATE DATABASE gestion_tareas;
USE gestion_tareas;

-- =========================
-- TABLA TRABAJADORES
-- =========================
CREATE TABLE trabajadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    puesto VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- TABLA TAREAS
-- =========================
CREATE TABLE tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    
    estado ENUM('pendiente', 'en_progreso', 'completada') DEFAULT 'pendiente',
    prioridad ENUM('baja', 'media', 'alta') DEFAULT 'media',

    trabajador_id INT,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (trabajador_id) REFERENCES trabajadores(id) ON DELETE CASCADE
);

-- =========================
-- INSERT TRABAJADORES
-- =========================
INSERT INTO trabajadores (nombre, email, puesto) VALUES
('Ana Pérez', 'ana@email.com', 'Administrativa'),
('Juan Martínez', 'juan@email.com', 'Técnico'),
('Laura Sánchez', 'laura@email.com', 'Gestora de proyectos');

-- =========================
-- INSERT TAREAS
-- =========================
INSERT INTO tareas (titulo, descripcion, estado, prioridad, trabajador_id) VALUES
('Revisar correos', 'Responder emails pendientes', 'pendiente', 'media', 1),
('Preparar informe', 'Informe mensual de actividad', 'en_progreso', 'alta', 1),

('Instalación equipo', 'Montaje en cliente', 'pendiente', 'alta', 2),
('Mantenimiento sistema', 'Revisión periódica', 'completada', 'media', 2),

('Planificación proyecto', 'Definir tareas y tiempos', 'pendiente', 'alta', 3),
('Reunión equipo', 'Coordinar avances', 'en_progreso', 'baja', 3);
