-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-12-2025 a las 09:10:42
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
-- Base de datos: `veterinaria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE DATABASE IF NOT EXISTS `veterinaria` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `veterinaria`;


CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `fecha_alta` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `apellidos`, `telefono`, `email`, `direccion`, `fecha_alta`) VALUES
(42, 'Ana', 'García López', '600111001', 'ana.garcia@email.com', 'Calle Mayor 1', '2025-12-14 15:40:34'),
(43, 'Luis', 'Pérez Martín', '600111002', 'luis.perez@email.com', 'Avenida Andalucía 12', '2025-12-14 15:40:34'),
(44, 'María', 'Ruiz Torres', '600111003', 'maria.ruiz@email.com', 'Calle Luna 5', '2025-12-14 15:40:34'),
(45, 'Carlos', 'Sánchez Vega', '600111004', 'carlos.sanchez@email.com', 'Calle Sol 8', '2025-12-14 15:40:34'),
(46, 'Laura', 'Fernández Gil', '600111005', 'laura.fernandez@email.com', 'Plaza España 3', '2025-12-14 15:40:34'),
(47, 'Javier', 'Moreno Díaz', '600111006', 'javier.moreno@email.com', 'Calle Real 22', '2025-12-14 15:40:34'),
(48, 'Lucía', 'Romero Cruz', '600111007', 'lucia.romero@email.com', 'Calle Olivo 9', '2025-12-14 15:40:34'),
(49, 'David', 'Navarro Ortiz', '600111008', 'david.navarro@email.com', 'Avenida del Mar 15', '2025-12-14 15:40:34'),
(50, 'Elena', 'Molina Reyes', '600111009', 'elena.molina@email.com', 'Calle Jardín 4', '2025-12-14 15:40:34'),
(51, 'Pablo', 'Castro León', '600111010', 'pablo.castro@email.com', 'Calle Norte 7', '2025-12-14 15:40:34'),
(52, 'Sara', 'Vargas Soto', '600111011', 'sara.vargas@email.com', 'Calle Sur 18', '2025-12-14 15:40:34'),
(53, 'Miguel', 'Ramos Peña', '600111012', 'miguel.ramos@email.com', 'Calle Río 2', '2025-12-14 15:40:34'),
(54, 'Carmen', 'Ibáñez Flores', '600111013', 'carmen.ibanez@email.com', 'Calle Sierra 11', '2025-12-14 15:40:34'),
(55, 'Álvaro', 'Nieto Campos', '600111014', 'alvaro.nieto@email.com', 'Avenida Constitución 6', '2025-12-14 15:40:34'),
(56, 'Isabel', 'Cano Fuentes', '600111015', 'isabel.cano@email.com', 'Calle Prado 14', '2025-12-14 15:40:34'),
(57, 'Raúl', 'Herrera Moya', '600111016', 'raul.herrera@email.com', 'Calle Estación 20', '2025-12-14 15:40:34'),
(58, 'Patricia', 'Serrano Vidal', '600111017', 'patricia.serrano@email.com', 'Calle Molino 10', '2025-12-14 15:40:34'),
(59, 'Diego', 'Aguilar Ríos', '600111018', 'diego.aguilar@email.com', 'Plaza Mayor 1', '2025-12-14 15:40:34'),
(60, 'Natalia', 'Blanco Suárez', '600111019', 'natalia.blanco@email.com', 'Calle Fuente 13', '2025-12-14 15:40:34'),
(61, 'Antonio', 'Lorenzo Pardo', '600111020', 'antonio.lorenzo@email.com', 'Calle Camino 16', '2025-12-14 15:40:34'),
(67, 'rafa', 'crdr', '691018650', 'corderorafa0@gmail.com', 'asba', '2025-12-15 10:33:27'),
(68, 'asdf', 'asdf', '691018650', 'cordero@gmail.com', 'asdfasdf', '2025-12-18 11:32:37');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nombre` (`nombre`),
  ADD KEY `idx_telefono` (`telefono`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
