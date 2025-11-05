-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-11-2025 a las 20:49:06
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;
/*!40101 SET NAMES utf8mb4 */
;

--
-- Base de datos: `abc`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
    `id` int(11) NOT NULL,
    `nif` varchar(15) NOT NULL,
    `persona_contacto` varchar(100) NOT NULL,
    `telefono_contacto` varchar(50) NOT NULL,
    `correo_electronico` varchar(100) NOT NULL,
    `direccion` varchar(255) DEFAULT NULL,
    `poblacion` varchar(100) DEFAULT NULL,
    `codigo_postal` char(5) DEFAULT NULL,
    `provincia_codigo` char(2) NOT NULL,
    `descripcion` text NOT NULL,
    `estado` char(1) NOT NULL,
    `operario_encargado_id` int(11) NOT NULL,
    `fecha_creacion` datetime NOT NULL,
    `fecha_realizacion` date DEFAULT NULL,
    `anotaciones_anteriores` text DEFAULT NULL,
    `anotaciones_posteriores` text DEFAULT NULL,
    `fichero_adjunto_nombre_original` varchar(255) DEFAULT NULL,
    `fichero_adjunto_fecha` datetime DEFAULT NULL,
    `fichero_adjunto_operario_id` int(11) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
    `id` int(11) NOT NULL,
    `usuario` varchar(50) NOT NULL,
    `clave` varchar(255) NOT NULL,
    `rol` enum('Administrador', 'Operario') NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO
    `usuarios` (
        `id`,
        `usuario`,
        `clave`,
        `rol`
    )
VALUES (
        1,
        'admin',
        'admin',
        'Administrador'
    ),
    (
        2,
        'operario',
        'operario',
        'Operario'
    );

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `tareas`
--
ALTER TABLE `tareas`
ADD PRIMARY KEY (`id`),
ADD KEY `operario_encargado_id` (`operario_encargado_id`),
ADD KEY `fichero_adjunto_operario_id` (`fichero_adjunto_operario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `tareas`
--
ALTER TABLE `tareas` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `tareas`
--
ALTER TABLE `tareas`
ADD CONSTRAINT `tareas_ibfk_1` FOREIGN KEY (`operario_encargado_id`) REFERENCES `usuarios` (`id`),
ADD CONSTRAINT `tareas_ibfk_2` FOREIGN KEY (`fichero_adjunto_operario_id`) REFERENCES `usuarios` (`id`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;