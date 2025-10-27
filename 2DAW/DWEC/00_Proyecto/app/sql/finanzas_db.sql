-- Script de creación de base de datos: finanzas_db
-- Requisitos: MySQL 5.7+

CREATE DATABASE IF NOT EXISTS `finanzas_db`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `finanzas_db`;

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `fecha_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de finanzas (ingresos y gastos)
CREATE TABLE IF NOT EXISTS `finanzas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` INT UNSIGNED NOT NULL,
  `tipo` ENUM('ingreso','gasto') NOT NULL,
  `monto` DECIMAL(10,2) NOT NULL,
  `descripcion` VARCHAR(255) NULL,
  `fecha_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_usuario` (`id_usuario`),
  CONSTRAINT `fk_finanzas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Índices adicionales para consultas por fecha
CREATE INDEX IF NOT EXISTS `idx_finanzas_fecha` ON `finanzas` (`fecha_registro`);