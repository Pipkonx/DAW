-- SQL para la Base de Datos de la Agencia de Seguros
-- Estructura y datos iniciales

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- 1. Usuarios
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `10_agenciaseguros_usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `10_agenciaseguros_usuarios` (`username`, `password`) VALUES
('admin', '123'),
('user', 'user');

-- --------------------------------------------------------
-- 2. Clientes
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `10_agenciaseguros_clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `localidad` varchar(100) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `tipo` enum('Particular','Empresa') DEFAULT 'Particular',
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `10_agenciaseguros_clientes` (`codigo`, `nombre`, `telefono`, `localidad`, `cp`, `provincia`, `tipo`) VALUES
('C001', 'Juan Pérez', '600111222', 'Almería', '04001', 'Almería', 'Particular'),
('C002', 'Seguros S.A.', '912334455', 'Málaga', '29001', 'Málaga', 'Empresa');

-- --------------------------------------------------------
-- 3. Pólizas
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `10_agenciaseguros_polizas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) DEFAULT NULL,
  `numero` varchar(20) NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `estado` enum('cobrada','a cuenta','liquidada','anulada','pre-anulada') DEFAULT 'a cuenta',
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero` (`numero`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `10_agenciaseguros_polizas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `10_agenciaseguros_clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `10_agenciaseguros_polizas` (`cliente_id`, `numero`, `importe`, `fecha`, `estado`, `observaciones`) VALUES
(1, 'POL-001', 500.00, '2023-10-01', 'cobrada', 'Pago puntual'),
(1, 'POL-002', 1200.00, '2023-12-15', 'a cuenta', 'Pendiente segundo pago');

-- --------------------------------------------------------
-- 4. Pagos / Recibos
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `10_agenciaseguros_pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `poliza_id` int(11) DEFAULT NULL,
  `fecha` date NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `poliza_id` (`poliza_id`),
  CONSTRAINT `10_agenciaseguros_pagos_ibfk_1` FOREIGN KEY (`poliza_id`) REFERENCES `10_agenciaseguros_polizas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `10_agenciaseguros_pagos` (`poliza_id`, `fecha`, `importe`) VALUES
(2, '2023-12-16', 400.00),
(2, '2023-12-20', 200.00);

-- --------------------------------------------------------
-- 5. Geografía (Provincias)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `provincias` (
  `id` int(11) NOT NULL,
  `provincia` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `provincias` (`id`, `provincia`) VALUES
(4, 'Almería'),
(11, 'Cádiz'),
(14, 'Córdoba'),
(18, 'Granada'),
(21, 'Huelva'),
(23, 'Jaén'),
(29, 'Málaga'),
(41, 'Sevilla');

-- --------------------------------------------------------
-- 6. Geografía (Municipios)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `municipios` (
  `id` int(11) NOT NULL,
  `provincia_id` int(11) DEFAULT NULL,
  `municipio` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Ejemplo de datos para Almería (4) y Málaga (29)
INSERT INTO `municipios` (`id`, `provincia_id`, `municipio`) VALUES
(4001, 4, 'Abla'),
(4002, 4, 'Abrucena'),
(4003, 4, 'Adra'),
(4013, 4, 'Almería'),
(4041, 4, 'El Ejido'),
(4066, 4, 'Níjar'),
(4079, 4, 'Roquetas de Mar'),
(29001, 29, 'Alameda'),
(29007, 29, 'Alhaurín de la Torre'),
(29054, 29, 'Fuengirola'),
(29067, 29, 'Málaga'),
(29069, 29, 'Marbella'),
(29070, 29, 'Mijas'),
(29094, 29, 'Vélez-Málaga');

COMMIT;
