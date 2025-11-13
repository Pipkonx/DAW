-- Ajusta el nombre de la base si quieres; por defecto BD.php usa DB_DATABASE del .env (por ejemplo 'laravel')
CREATE DATABASE IF NOT EXISTS `laravel` DEFAULT CHARACTER SET utf8mb4 DEFAULT COLLATE utf8mb4_spanish_ci;

USE `laravel`;

-- Eliminamos si existe para crear limpio
DROP TABLE IF EXISTS `tareas`;

-- Tabla principal usada por el modelo Tareas (CRUD completo)
CREATE TABLE `tareas` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `nifCif` VARCHAR(20) NOT NULL, -- NIF/CIF, validado por código
    `personaNombre` VARCHAR(100) NOT NULL, -- Persona de contacto
    `telefono` VARCHAR(30) NOT NULL, -- Teléfono, validado por código
    `correo` VARCHAR(150) NOT NULL, -- Email, validado por código
    `descripcionTarea` TEXT NOT NULL, -- Descripción
    `direccionTarea` VARCHAR(200) DEFAULT '', -- Dirección
    `poblacion` VARCHAR(100) DEFAULT '', -- Población
    `codigoPostal` VARCHAR(10) DEFAULT '', -- CP (validación en app)
    `provincia` VARCHAR(5) NOT NULL, -- Código de provincia (e.g. '28' Madrid)
    `estadoTarea` ENUM('B', 'P', 'R', 'C') NOT NULL, -- B=Esperando aprobación, P=Pendiente, R=Realizada, C=Cancelada
    `operarioEncargado` VARCHAR(100) DEFAULT '', -- Operario encargado
    `fechaRealizacion` DATE NOT NULL, -- Fecha de realización (debe ser posterior a hoy, validado en app)
    `anotacionesAnteriores` TEXT DEFAULT NULL, -- Notas anteriores
    `anotacionesPosteriores` TEXT DEFAULT NULL, -- Notas posteriores
    PRIMARY KEY (`id`),
    KEY `idx_tareas_estado` (`estadoTarea`),
    KEY `idx_tareas_fecha` (`fechaRealizacion`),
    KEY `idx_tareas_persona` (`personaNombre`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_spanish_ci;

-- Datos de ejemplo para probar el flujo de alta/listado/edición/eliminación
INSERT INTO
    `tareas` (
        `nifCif`,
        `personaNombre`,
        `telefono`,
        `correo`,
        `descripcionTarea`,
        `direccionTarea`,
        `poblacion`,
        `codigoPostal`,
        `provincia`,
        `estadoTarea`,
        `operarioEncargado`,
        `fechaRealizacion`,
        `anotacionesAnteriores`,
        `anotacionesPosteriores`
    )
VALUES (
        '12345678Z',
        'Juan Pérez',
        '+34 600 000 001',
        'juan@example.com',
        'Instalación de equipo',
        'C/ Mayor 1',
        'Madrid',
        '28001',
        '28',
        'P',
        'Juan Pérez',
        DATE_ADD(CURDATE(), INTERVAL 7 DAY),
        '',
        ''
    ),
    (
        'B12345678',
        'María López',
        '+34 600 000 002',
        'maria@example.com',
        'Revisión de sistema',
        'Av. Sol 3',
        'Sevilla',
        '41001',
        '41',
        'B',
        'María López',
        DATE_ADD(CURDATE(), INTERVAL 10 DAY),
        '',
        ''
    );