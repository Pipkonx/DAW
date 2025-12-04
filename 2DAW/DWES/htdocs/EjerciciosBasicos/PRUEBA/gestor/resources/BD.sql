-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-11-2025 a las 11:49:54
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `laravel`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `id` int(10) UNSIGNED NOT NULL,
  `nifCif` varchar(20) NOT NULL,
  `personaNombre` varchar(100) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `descripcionTarea` text NOT NULL,
  `direccionTarea` varchar(200) DEFAULT '',
  `poblacion` varchar(100) DEFAULT '',
  `codigoPostal` varchar(10) DEFAULT '',
  `provincia` varchar(5) NOT NULL,
  `estadoTarea` enum('B','P','R','C') NOT NULL,
  `operarioEncargado` varchar(100) DEFAULT '',
  `fechaRealizacion` date NOT NULL,
  `anotacionesAnteriores` text DEFAULT NULL,
  `anotacionesPosteriores` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `tareas`
--

INSERT INTO `tareas` (`id`, `nifCif`, `personaNombre`, `telefono`, `correo`, `descripcionTarea`, `direccionTarea`, `poblacion`, `codigoPostal`, `provincia`, `estadoTarea`, `operarioEncargado`, `fechaRealizacion`, `anotacionesAnteriores`, `anotacionesPosteriores`) VALUES
(1, '12345678A', 'María López', '+34 666 777 888', 'maria.lopez@empresa.es', 'Revisión de cableado', 'Calle San Bernardo 45', 'Madrid', '28015', '28', 'P', 'Laura Fernández', '2025-12-02', 'Revisar conexiones', 'Todo correcto'),
(2, '87654321B', 'Carlos Gómez', '+34 655 444 333', 'carlos.gomez@cliente.com', 'Configuración de router', 'Avenida de América 12', 'Barcelona', '08029', '08', 'R', 'Miguel Sánchez', '2025-12-03', 'Acceso remoto habilitado', 'Configuración finalizada'),
(3, '11223344C', 'Lucía Fernández', '+34 677 222 111', 'lucia.fernandez@mail.es', 'Instalación de cámaras IP', 'Calle de la Princesa 8', 'Valencia', '46001', '46', 'C', 'Elena Gómez', '2025-12-04', 'Cámaras entregadas', 'Cliente satisfecho'),
(4, '99887766D', 'Javier Martín', '+34 688 999 000', 'javier.martin@tech.com', 'Mantenimiento de servidor', 'Paseo de Recoletos 15', 'Sevilla', '41001', '41', 'B', 'Ana García', '2025-12-05', 'Backup realizado', 'Pendiente de revisión'),
(5, 'Y23456789', 'Ana García', '+34 622 222 222', 'ana.garcia@example.com', 'Mantenimiento preventivo', 'Plaza España 5', 'Barcelona', '08001', '08', 'B', 'Laura Fernández', '2025-12-05', '', ''),
(6, 'Z34567890', 'Pedro Martínez', '+34 633 333 333', 'pedro.martinez@example.com', 'Reparación de equipo', 'Av. de la Constitución 20', 'Valencia', '46002', '46', 'C', 'Miguel Sánchez', '2025-12-10', 'Pieza solicitada', ''),
(7, 'A45678901', 'Sofía Ruiz', '+34 644 444 444', 'sofia.ruiz@example.com', 'Configuración de software', 'Paseo de Gracia 15', 'Sevilla', '41002', '41', '', 'Elena Gómez', '2025-12-12', '', 'Pendiente de confirmación'),
(8, 'B56789012', 'David López', '+34 655 555 555', 'david.lopez@example.com', 'Auditoría de seguridad', 'Gran Vía 30', 'Zaragoza', '50001', '50', 'B', 'Javier Martín', '2025-12-15', 'Acceso concedido', ''),
(9, 'C67890123', 'Laura Fernández', '+34 666 666 666', 'laura.fernandez@example.com', 'Actualización de sistema', 'Calle del Sol 5', 'Málaga', '29001', '29', '', 'Ana García', '2025-12-18', '', ''),
(10, 'D78901234', 'Miguel Sánchez', '+34 677 777 777', 'miguel.sanchez@example.com', 'Soporte técnico', 'Av. Diagonal 50', 'Murcia', '30001', '30', 'C', 'Pedro Martínez', '2025-12-20', 'Problema recurrente', ''),
(11, 'E89012345', 'Elena Gómez', '+34 688 888 888', 'elena.gomez@example.com', 'Instalación de software', 'Rambla de Catalunya 25', 'Palma', '07001', '07', '', 'Sofía Ruiz', '2026-01-05', '', ''),
(12, 'F90123456', 'Javier Martín', '+34 699 999 999', 'javier.martin@example.com', 'Revisión de hardware', 'Paseo de la Castellana 100', 'Bilbao', '48001', '48', 'B', 'David López', '2026-01-08', 'Componentes pedidos', ''),
(13, 'G01234567', 'Carlos Ruiz', '+34 600 111 222', 'carlos.ruiz@example.com', 'Formación de usuario', 'Calle Larios 1', 'Alicante', '03001', '03', '', 'Juan Pérez', '2026-01-10', '', 'Sesión programada'),
(14, 'H12345679', 'Isabel Torres', '+34 601 222 333', 'isabel.torres@example.com', 'Desarrollo de módulo', 'Av. del Puerto 15', 'Córdoba', '14001', '14', 'B', 'Ana García', '2026-01-15', 'Requisitos definidos', ''),
(15, 'I23456780', 'Ricardo Vargas', '+34 602 333 444', 'ricardo.vargas@example.com', 'Migración de datos', 'Calle Betis 5', 'Valladolid', '47001', '47', 'C', 'Miguel Sánchez', '2026-01-18', 'Copia de seguridad realizada', ''),
(16, 'J34567891', 'Patricia Castro', '+34 603 444 555', 'patricia.castro@example.com', 'Instalación de cámaras', 'Paseo de Colón 10', 'Gijón', '33201', '33', '', 'Elena Gómez', '2026-01-20', '', ''),
(17, 'K45678902', 'Fernando Díaz', '+34 604 555 666', 'fernando.diaz@example.com', 'Revisión de licencias', 'Calle Sierpes 20', 'Vigo', '36201', '36', 'B', 'Javier Martín', '2026-01-22', 'Documentación pendiente', ''),
(18, 'L56789013', 'Marta Romero', '+34 605 666 777', 'marta.romero@example.com', 'Configuración de impresoras', 'Av. de la Palmera 50', 'Granada', '18001', '18', '', 'Carlos Ruiz', '2026-01-25', '', ''),
(19, 'M67890124', 'Sergio Blanco', '+34 606 777 888', 'sergio.blanco@example.com', 'Asesoramiento técnico', 'Calle Recogidas 10', 'Cartagena', '30201', '30', 'C', 'Isabel Torres', '2026-01-28', 'Reunión programada', ''),
(20, 'N78901235', 'Lucía Morales', '+34 607 888 999', 'lucia.morales@example.com', 'Instalación de servidor', 'Paseo del Prado 1', 'Santander', '39001', '39', 'B', 'Ricardo Vargas', '2026-02-01', 'Hardware recibido', ''),
(21, 'O89012346', 'Jorge Gil', '+34 608 999 000', 'jorge.gil@example.com', 'Desarrollo de API', 'Calle Alcalá 100', 'Salamanca', '37001', '37', '', 'Patricia Castro', '2026-02-05', '', 'Fase de pruebas'),
(22, 'P90123457', 'Andrea Soto', '+34 609 000 111', 'andrea.soto@example.com', 'Mantenimiento de base de datos', 'Plaza Mayor 1', 'Logroño', '26001', '26', 'C', 'Fernando Díaz', '2026-02-08', 'Optimización necesaria', ''),
(23, 'Q01234568', 'Roberto Vega', '+34 610 111 222', 'roberto.vega@example.com', 'Implementación de ERP', 'Calle Mayor 50', 'Oviedo', '33001', '33', 'B', 'Marta Romero', '2026-02-10', 'Reunión inicial', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `sesion` varchar(255) NOT NULL,
  `guardar_clave` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `contraseña`, `sesion`, `guardar_clave`) VALUES
(2, 'admin', 'admin', '', 1),
(3, 'operario', 'operario', '', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tareas`
--
ALTER TABLE `tareas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tareas_estado` (`estadoTarea`),
  ADD KEY `idx_tareas_fecha` (`fechaRealizacion`),
  ADD KEY `idx_tareas_persona` (`personaNombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
