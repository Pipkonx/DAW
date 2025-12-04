-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 01-12-2025 a las 17:28:31
-- Versión del servidor: 10.11.13-MariaDB-0ubuntu0.24.04.1
-- Versión de PHP: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `rafaelcordero`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tareas`
--

CREATE TABLE `tareas` (
  `id` bigint(20) UNSIGNED NOT NULL,
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
(1, '67716376B', 'Raúl Medina', '+34 688343386', 'raúl.medina@example.com', 'Tarea de mantenimiento #1', 'Calle Falsa 99', 'Málaga', '37852', '41', 'R', 'Carlos Ruiz', '2025-11-21', 'Nota anterior 1', ''),
(2, '95811996C', 'Iván López', '+34 697963759', 'iván.lópez@example.com', 'Tarea de mantenimiento #2', 'Calle Falsa 99', 'Cádiz', '21228', '11', 'C', 'Patricia Castro', '2025-11-22', 'Nota anterior 2', 'Nota posterior 2'),
(3, '18305695D', 'Pedro Salas', '+34 633918993', 'pedro.salas@example.com', 'Tarea de mantenimiento #3', 'Calle Falsa 13', 'Huelva', '22737', '45', 'R', 'David López', '2025-11-23', 'Nota anterior 3', 'Nota posterior 3'),
(4, '59642649E', 'Raúl Medina', '+34 637346229', 'raúl.medina@example.com', 'Tarea de mantenimiento #4', 'Calle Falsa 44', 'Vigo', '31675', '36', 'P', 'Patricia Castro', '2025-11-24', 'Nota anterior 4', 'Nota posterior 4'),
(5, '15044876F', 'Pedro Salas', '+34 654420132', 'pedro.salas@example.com', 'Tarea de mantenimiento #5', 'Calle Falsa 12', 'Toledo', '38515', '41', 'R', 'Javier Martín', '2025-11-25', 'Nota anterior 5', 'Nota posterior 5'),
(6, '73550196G', 'Iván López', '+34 641058745', 'iván.lópez@example.com', 'Tarea de mantenimiento #6', 'Calle Falsa 93', 'Toledo', '28253', '14', 'P', 'Ana García', '2025-11-26', 'Nota anterior 6', 'Nota posterior 6'),
(7, '54732801H', 'María Delgado', '+34 669503954', 'maría.delgado@example.com', 'Tarea de mantenimiento #7', 'Calle Falsa 45', 'Cádiz', '48446', '36', 'C', 'Patricia Castro', '2025-11-27', 'Nota anterior 7', 'Nota posterior 7'),
(8, '55902384I', 'Lucía Herrera', '+34 691375479', 'lucía.herrera@example.com', 'Tarea de mantenimiento #8', 'Calle Falsa 8', 'Valencia', '18250', '18', 'P', 'Carlos Ruiz', '2025-11-28', 'Nota anterior 8', 'Nota posterior 8'),
(9, '94576787J', 'María Delgado', '+34 677159591', 'maría.delgado@example.com', 'Tarea de mantenimiento #9', 'Calle Falsa 91', 'Vigo', '21934', '18', 'B', 'David López', '2025-11-29', 'Nota anterior 9', 'Nota posterior 9'),
(10, '28454144K', 'Lucía Herrera', '+34 620202169', 'lucía.herrera@example.com', 'Tarea de mantenimiento #10', 'Calle Falsa 28', 'Vigo', '15001', '45', 'C', 'Patricia Castro', '2025-11-30', 'Nota anterior 10', 'Nota posterior 10'),
(11, '69114299L', 'Laura Gómez', '+34 694599644', 'laura.gómez@example.com', 'Tarea de mantenimiento #11', 'Calle Falsa 25', 'Málaga', '39794', '18', 'P', 'Ana García', '2025-12-01', 'Nota anterior 11', 'Nota posterior 11'),
(12, '91216756M', 'Iván López', '+34 662976679', 'iván.lópez@example.com', 'Tarea de mantenimiento #12', 'Calle Falsa 66', 'Sevilla', '33553', '14', 'P', 'David López', '2025-12-02', 'Nota anterior 12', 'Nota posterior 12'),
(13, '14979828N', 'Laura Gómez', '+34 616261741', 'laura.gómez@example.com', 'Tarea de mantenimiento #13', 'Calle Falsa 48', 'Cádiz', '34678', '21', 'R', 'Javier Martín', '2025-12-03', 'Nota anterior 13', 'Nota posterior 13'),
(14, '81616584O', 'Lucía Herrera', '+34 633960780', 'lucía.herrera@example.com', 'Tarea de mantenimiento #14', 'Calle Falsa 49', 'Cádiz', '24425', '46', 'R', 'Carlos Ruiz', '2025-12-04', 'Nota anterior 14', 'Nota posterior 14'),
(15, '66744038P', 'Pedro Salas', '+34 645145894', 'pedro.salas@example.com', 'Tarea de mantenimiento #15', 'Calle Falsa 64', 'Bilbao', '20689', '18', 'B', 'Javier Martín', '2025-12-05', 'Nota anterior 15', 'Nota posterior 15'),
(16, '62956536Q', 'Mario Ortega', '+34 601817110', 'mario.ortega@example.com', 'Tarea de mantenimiento #16', 'Calle Falsa 37', 'Vigo', '31301', '41', 'C', 'Carlos Ruiz', '2025-12-06', 'Nota anterior 16', 'Nota posterior 16'),
(17, '95272398R', 'María Delgado', '+34 649767848', 'maría.delgado@example.com', 'Tarea de mantenimiento #17', 'Calle Falsa 22', 'Cádiz', '11005', '29', 'P', 'Javier Martín', '2025-12-07', 'Nota anterior 17', 'Nota posterior 17'),
(18, '73974738S', 'Iván López', '+34 663976377', 'iván.lópez@example.com', 'Tarea de mantenimiento #18', 'Calle Falsa 56', 'Toledo', '29212', '14', 'R', 'Carlos Ruiz', '2025-12-08', 'Nota anterior 18', 'Nota posterior 18'),
(19, '61029945T', 'Iván López', '+34 685777765', 'iván.lópez@example.com', 'Tarea de mantenimiento #19', 'Calle Falsa 95', 'Córdoba', '41891', '36', 'R', 'Carlos Ruiz', '2025-12-09', 'Nota anterior 19', 'Nota posterior 19'),
(20, '49061941U', 'Sofía Ruiz', '+34 624796997', 'sofía.ruiz@example.com', 'Tarea de mantenimiento #20', 'Calle Falsa 47', 'Huelva', '31194', '45', 'B', 'Carlos Ruiz', '2025-12-10', 'Nota anterior 20', 'Nota posterior 20'),
(21, '73494450V', 'Iván López', '+34 664679991', 'iván.lópez@example.com', 'Tarea de mantenimiento #21', 'Calle Falsa 14', 'Huelva', '31694', '48', 'R', 'Javier Martín', '2025-12-11', 'Nota anterior 21', 'Nota posterior 21'),
(22, '30300923W', 'Lucía Herrera', '+34 633707336', 'lucía.herrera@example.com', 'Tarea de mantenimiento #22', 'Calle Falsa 100', 'Bilbao', '26180', '29', 'R', 'Carlos Ruiz', '2025-12-12', 'Nota anterior 22', 'Nota posterior 22'),
(23, '86669538X', 'Iván López', '+34 671996238', 'iván.lópez@example.com', 'Tarea de mantenimiento #23', 'Calle Falsa 41', 'Bilbao', '14987', '18', 'B', 'Ana García', '2025-12-13', 'Nota anterior 23', 'Nota posterior 23'),
(24, '45847617Y', 'Iván López', '+34 622951693', 'iván.lópez@example.com', 'Tarea de mantenimiento #24', 'Calle Falsa 19', 'Toledo', '47887', '21', 'P', 'Ana García', '2025-12-14', 'Nota anterior 24', 'Nota posterior 24'),
(25, '59762900Z', 'Sofía Ruiz', '+34 679712145', 'sofía.ruiz@example.com', 'Tarea de mantenimiento #25', 'Calle Falsa 18', 'Vigo', '11552', '29', 'P', 'Ana García', '2025-12-15', 'Nota anterior 25', 'Nota posterior 25'),
(26, '61777870A', 'Pedro Salas', '+34 642376168', 'pedro.salas@example.com', 'Tarea de mantenimiento #26', 'Calle Falsa 35', 'Cádiz', '46354', '14', 'R', 'David López', '2025-12-16', 'Nota anterior 26', 'Nota posterior 26'),
(27, '31043193B', 'Sofía Ruiz', '+34 687379124', 'sofía.ruiz@example.com', 'Tarea de mantenimiento #27', 'Calle Falsa 74', 'Cádiz', '14791', '46', 'P', 'Carlos Ruiz', '2025-12-17', 'Nota anterior 27', 'Nota posterior 27'),
(28, '78978494C', 'Iván López', '+34 655917444', 'iván.lópez@example.com', 'Tarea de mantenimiento #28', 'Calle Falsa 98', 'Valencia', '19663', '46', 'C', 'Miguel Sánchez', '2025-12-18', 'Nota anterior 28', 'Nota posterior 28'),
(29, '52459317D', 'Sandra Núñez', '+34 626909290', 'sandra.núñez@example.com', 'Tarea de mantenimiento #29', 'Calle Falsa 56', 'Vigo', '17092', '21', 'B', 'Carlos Ruiz', '2025-12-19', 'Nota anterior 29', 'Nota posterior 29'),
(30, '10698946E', 'Pedro Salas', '+34 641412658', 'pedro.salas@example.com', 'Tarea de mantenimiento #30', 'Calle Falsa 30', 'Málaga', '44513', '41', 'P', 'David López', '2025-12-20', 'Nota anterior 30', 'Nota posterior 30'),
(31, '64153835F', 'Lucía Herrera', '+34 676341038', 'lucía.herrera@example.com', 'Tarea de mantenimiento #31', 'Calle Falsa 10', 'Málaga', '10068', '14', 'P', 'Ana García', '2025-12-21', 'Nota anterior 31', 'Nota posterior 31'),
(32, '66712502G', 'Laura Gómez', '+34 605231001', 'laura.gómez@example.com', 'Tarea de mantenimiento #32', 'Calle Falsa 44', 'Huelva', '29593', '48', 'C', 'Javier Martín', '2025-12-22', 'Nota anterior 32', 'Nota posterior 32'),
(33, '63399456H', 'Lucía Herrera', '+34 686073336', 'lucía.herrera@example.com', 'Tarea de mantenimiento #33', 'Calle Falsa 97', 'Málaga', '36931', '45', 'B', 'Ana García', '2025-12-23', 'Nota anterior 33', 'Nota posterior 33'),
(34, '72431328I', 'Lucía Herrera', '+34 613879021', 'lucía.herrera@example.com', 'Tarea de mantenimiento #34', 'Calle Falsa 16', 'Huelva', '39801', '36', 'R', 'Miguel Sánchez', '2025-12-24', 'Nota anterior 34', 'Nota posterior 34'),
(35, '44212209J', 'Lucía Herrera', '+34 622078829', 'lucía.herrera@example.com', 'Tarea de mantenimiento #35', 'Calle Falsa 31', 'Toledo', '24530', '36', 'R', 'Patricia Castro', '2025-12-25', 'Nota anterior 35', 'Nota posterior 35'),
(36, '70049820K', 'Pedro Salas', '+34 684546378', 'pedro.salas@example.com', 'Tarea de mantenimiento #36', 'Calle Falsa 46', 'Toledo', '32819', '36', 'P', 'Javier Martín', '2025-12-26', 'Nota anterior 36', 'Nota posterior 36'),
(37, '69589544L', 'Iván López', '+34 649705847', 'iván.lópez@example.com', 'Tarea de mantenimiento #37', 'Calle Falsa 42', 'Málaga', '46584', '14', 'P', 'Ana García', '2025-12-27', 'Nota anterior 37', 'Nota posterior 37'),
(38, '34496831M', 'Raúl Medina', '+34 669760403', 'raúl.medina@example.com', 'Tarea de mantenimiento #38', 'Calle Falsa 84', 'Sevilla', '44609', '45', 'C', 'Patricia Castro', '2025-12-28', 'Nota anterior 38', 'Nota posterior 38'),
(39, '54168952N', 'María Delgado', '+34 658888983', 'maría.delgado@example.com', 'Tarea de mantenimiento #39', 'Calle Falsa 59', 'Cádiz', '21862', '46', 'C', 'Javier Martín', '2025-12-29', 'Nota anterior 39', 'Nota posterior 39'),
(40, '23589630O', 'Pedro Salas', '+34 699222902', 'pedro.salas@example.com', 'Tarea de mantenimiento #40', 'Calle Falsa 26', 'Huelva', '17919', '29', 'B', 'Miguel Sánchez', '2025-12-30', 'Nota anterior 40', 'Nota posterior 40'),
(41, '51518456P', 'Pedro Salas', '+34 627494659', 'pedro.salas@example.com', 'Tarea de mantenimiento #41', 'Calle Falsa 21', 'Sevilla', '14752', '11', 'P', 'Carlos Ruiz', '2025-12-31', 'Nota anterior 41', 'Nota posterior 41'),
(42, '57478953Q', 'Mario Ortega', '+34 681041582', 'mario.ortega@example.com', 'Tarea de mantenimiento #42', 'Calle Falsa 37', 'Córdoba', '11029', '45', 'R', 'David López', '2026-01-01', 'Nota anterior 42', 'Nota posterior 42'),
(43, '11159294R', 'Sandra Núñez', '+34 619888318', 'sandra.núñez@example.com', 'Tarea de mantenimiento #43', 'Calle Falsa 33', 'Córdoba', '41181', '45', 'B', 'Javier Martín', '2026-01-02', 'Nota anterior 43', 'Nota posterior 43'),
(44, '16877121S', 'José Manuel Torres', '+34 658268459', 'josé.manuel.torres@example.com', 'Tarea de mantenimiento #44', 'Calle Falsa 39', 'Bilbao', '24074', '18', 'B', 'Javier Martín', '2026-01-03', 'Nota anterior 44', 'Nota posterior 44'),
(45, '46628041T', 'Mario Ortega', '+34 686376733', 'mario.ortega@example.com', 'Tarea de mantenimiento #45', 'Calle Falsa 66', 'Valencia', '34082', '45', 'C', 'Patricia Castro', '2026-01-04', 'Nota anterior 45', 'Nota posterior 45'),
(46, '95858851U', 'Raúl Medina', '+34 666679375', 'raúl.medina@example.com', 'Tarea de mantenimiento #46', 'Calle Falsa 25', 'Huelva', '21439', '11', 'P', 'Carlos Ruiz', '2026-01-05', 'Nota anterior 46', 'Nota posterior 46'),
(47, '43009742V', 'María Delgado', '+34 654241799', 'maría.delgado@example.com', 'Tarea de mantenimiento #47', 'Calle Falsa 82', 'Vigo', '45702', '21', 'C', 'Carlos Ruiz', '2026-01-06', 'Nota anterior 47', 'Nota posterior 47'),
(48, '36315217W', 'María Delgado', '+34 686355723', 'maría.delgado@example.com', 'Tarea de mantenimiento #48', 'Calle Falsa 9', 'Cádiz', '34344', '14', 'C', 'Miguel Sánchez', '2026-01-07', 'Nota anterior 48', 'Nota posterior 48'),
(49, '89817751X', 'María Delgado', '+34 619481880', 'maría.delgado@example.com', 'Tarea de mantenimiento #49', 'Calle Falsa 45', 'Toledo', '32489', '11', 'R', 'Carlos Ruiz', '2026-01-08', 'Nota anterior 49', 'Nota posterior 49'),
(50, '59473664Y', 'Mario Ortega', '+34 649751393', 'mario.ortega@example.com', 'Tarea de mantenimiento #50', 'Calle Falsa 53', 'Vigo', '21864', '21', 'B', 'Patricia Castro', '2026-01-09', 'Nota anterior 50', 'Nota posterior 50'),
(51, '89588237Z', 'Laura Gómez', '+34 684482367', 'laura.gómez@example.com', 'Tarea de mantenimiento #51', 'Calle Falsa 91', 'Huelva', '28716', '36', 'R', 'Carlos Ruiz', '2026-01-10', 'Nota anterior 51', 'Nota posterior 51'),
(52, '99698704A', 'Sandra Núñez', '+34 616973283', 'sandra.núñez@example.com', 'Tarea de mantenimiento #52', 'Calle Falsa 79', 'Vigo', '28774', '45', 'C', 'Miguel Sánchez', '2026-01-11', 'Nota anterior 52', 'Nota posterior 52'),
(53, '48192707B', 'Laura Gómez', '+34 641101956', 'laura.gómez@example.com', 'Tarea de mantenimiento #53', 'Calle Falsa 35', 'Vigo', '44277', '11', 'C', 'Patricia Castro', '2026-01-12', 'Nota anterior 53', 'Nota posterior 53'),
(54, '70900884C', 'Lucía Herrera', '+34 674290435', 'lucía.herrera@example.com', 'Tarea de mantenimiento #54', 'Calle Falsa 48', 'Valencia', '28081', '14', 'C', 'Carlos Ruiz', '2026-01-13', 'Nota anterior 54', 'Nota posterior 54'),
(55, '73103866D', 'Iván López', '+34 685307195', 'iván.lópez@example.com', 'Tarea de mantenimiento #55', 'Calle Falsa 32', 'Toledo', '25209', '14', 'P', 'Carlos Ruiz', '2026-01-14', 'Nota anterior 55', 'Nota posterior 55'),
(56, '88512275E', 'Laura Gómez', '+34 690867949', 'laura.gómez@example.com', 'Tarea de mantenimiento #56', 'Calle Falsa 21', 'Huelva', '43930', '29', 'B', 'Carlos Ruiz', '2026-01-15', 'Nota anterior 56', 'Nota posterior 56'),
(57, '59719692F', 'Lucía Herrera', '+34 662582565', 'lucía.herrera@example.com', 'Tarea de mantenimiento #57', 'Calle Falsa 15', 'Sevilla', '20584', '45', 'B', 'Javier Martín', '2026-01-16', 'Nota anterior 57', 'Nota posterior 57'),
(58, '20789448G', 'Mario Ortega', '+34 693421701', 'mario.ortega@example.com', 'Tarea de mantenimiento #58', 'Calle Falsa 23', 'Málaga', '35328', '46', 'C', 'David López', '2026-01-17', 'Nota anterior 58', 'Nota posterior 58'),
(59, '81378786H', 'Mario Ortega', '+34 699807275', 'mario.ortega@example.com', 'Tarea de mantenimiento #59', 'Calle Falsa 45', 'Córdoba', '31156', '21', 'P', 'Carlos Ruiz', '2026-01-18', 'Nota anterior 59', 'Nota posterior 59'),
(60, '68348958I', 'Pedro Salas', '+34 688030675', 'pedro.salas@example.com', 'Tarea de mantenimiento #60', 'Calle Falsa 7', 'Toledo', '29880', '14', 'C', 'Miguel Sánchez', '2026-01-19', 'Nota anterior 60', 'Nota posterior 60'),
(61, '61664942J', 'Iván López', '+34 611217651', 'iván.lópez@example.com', 'Tarea de mantenimiento #61', 'Calle Falsa 19', 'Bilbao', '22954', '36', 'R', 'Ana García', '2026-01-20', 'Nota anterior 61', 'Nota posterior 61'),
(62, '90474688K', 'María Delgado', '+34 660441065', 'maría.delgado@example.com', 'Tarea de mantenimiento #62', 'Calle Falsa 26', 'Valencia', '21122', '18', 'B', 'Javier Martín', '2026-01-21', 'Nota anterior 62', 'Nota posterior 62'),
(63, '93458446L', 'Sofía Ruiz', '+34 656143941', 'sofía.ruiz@example.com', 'Tarea de mantenimiento #63', 'Calle Falsa 41', 'Cádiz', '22968', '29', 'B', 'Javier Martín', '2026-01-22', 'Nota anterior 63', 'Nota posterior 63'),
(64, '35919854M', 'Mario Ortega', '+34 623968507', 'mario.ortega@example.com', 'Tarea de mantenimiento #64', 'Calle Falsa 53', 'Córdoba', '45838', '14', 'P', 'Ana García', '2026-01-23', 'Nota anterior 64', 'Nota posterior 64'),
(65, '77656863N', 'Pedro Salas', '+34 699025895', 'pedro.salas@example.com', 'Tarea de mantenimiento #65', 'Calle Falsa 30', 'Toledo', '19842', '14', 'R', 'David López', '2026-01-24', 'Nota anterior 65', 'Nota posterior 65'),
(66, '13091475O', 'María Delgado', '+34 624455802', 'maría.delgado@example.com', 'Tarea de mantenimiento #66', 'Calle Falsa 17', 'Cádiz', '25588', '21', 'P', 'Carlos Ruiz', '2026-01-25', 'Nota anterior 66', 'Nota posterior 66'),
(67, '20377395P', 'Sandra Núñez', '+34 626103329', 'sandra.núñez@example.com', 'Tarea de mantenimiento #67', 'Calle Falsa 89', 'Granada', '11706', '41', 'R', 'David López', '2026-01-26', 'Nota anterior 67', 'Nota posterior 67'),
(68, '23495554Q', 'Laura Gómez', '+34 689008658', 'laura.gómez@example.com', 'Tarea de mantenimiento #68', 'Calle Falsa 74', 'Córdoba', '22631', '18', 'C', 'Patricia Castro', '2026-01-27', 'Nota anterior 68', 'Nota posterior 68'),
(69, '55161264R', 'Laura Gómez', '+34 635522063', 'laura.gómez@example.com', 'Tarea de mantenimiento #69', 'Calle Falsa 86', 'Vigo', '41699', '18', 'R', 'Ana García', '2026-01-28', 'Nota anterior 69', 'Nota posterior 69'),
(70, '24733374S', 'Sofía Ruiz', '+34 666876625', 'sofía.ruiz@example.com', 'Tarea de mantenimiento #70', 'Calle Falsa 46', 'Vigo', '48560', '46', 'R', 'David López', '2026-01-29', 'Nota anterior 70', 'Nota posterior 70'),
(71, '38223941T', 'Pedro Salas', '+34 609609430', 'pedro.salas@example.com', 'Tarea de mantenimiento #71', 'Calle Falsa 18', 'Bilbao', '28488', '45', 'R', 'Miguel Sánchez', '2026-01-30', 'Nota anterior 71', 'Nota posterior 71'),
(72, '71684105U', 'Raúl Medina', '+34 673525078', 'raúl.medina@example.com', 'Tarea de mantenimiento #72', 'Calle Falsa 92', 'Valencia', '16287', '48', 'C', 'Patricia Castro', '2026-01-31', 'Nota anterior 72', 'Nota posterior 72'),
(73, '90470276V', 'María Delgado', '+34 664285250', 'maría.delgado@example.com', 'Tarea de mantenimiento #73', 'Calle Falsa 64', 'Valencia', '26611', '29', 'B', 'Miguel Sánchez', '2026-02-01', 'Nota anterior 73', 'Nota posterior 73'),
(74, '40026236W', 'Sandra Núñez', '+34 688598051', 'sandra.núñez@example.com', 'Tarea de mantenimiento #74', 'Calle Falsa 60', 'Sevilla', '42062', '14', 'P', 'Patricia Castro', '2026-02-02', 'Nota anterior 74', 'Nota posterior 74'),
(75, '88632318X', 'Lucía Herrera', '+34 677605634', 'lucía.herrera@example.com', 'Tarea de mantenimiento #75', 'Calle Falsa 28', 'Granada', '12351', '41', 'R', 'David López', '2026-02-03', 'Nota anterior 75', 'Nota posterior 75'),
(76, '68857285Y', 'José Manuel Torres', '+34 616273535', 'josé.manuel.torres@example.com', 'Tarea de mantenimiento #76', 'Calle Falsa 18', 'Granada', '15858', '14', 'C', 'Javier Martín', '2026-02-04', 'Nota anterior 76', 'Nota posterior 76'),
(77, '10989804Z', 'Lucía Herrera', '+34 638950956', 'lucía.herrera@example.com', 'Tarea de mantenimiento #77', 'Calle Falsa 75', 'Toledo', '21067', '14', 'B', 'Carlos Ruiz', '2026-02-05', 'Nota anterior 77', 'Nota posterior 77'),
(78, '52418254A', 'Mario Ortega', '+34 647397878', 'mario.ortega@example.com', 'Tarea de mantenimiento #78', 'Calle Falsa 5', 'Huelva', '29429', '14', 'P', 'Miguel Sánchez', '2026-02-06', 'Nota anterior 78', 'Nota posterior 78'),
(79, '52359929B', 'Raúl Medina', '+34 616560699', 'raúl.medina@example.com', 'Tarea de mantenimiento #79', 'Calle Falsa 29', 'Bilbao', '45307', '46', 'C', 'Patricia Castro', '2026-02-07', 'Nota anterior 79', 'Nota posterior 79'),
(80, '95774099C', 'José Manuel Torres', '+34 617506401', 'josé.manuel.torres@example.com', 'Tarea de mantenimiento #80', 'Calle Falsa 37', 'Cádiz', '19952', '46', 'P', 'Patricia Castro', '2026-02-08', 'Nota anterior 80', 'Nota posterior 80'),
(81, '45736566D', 'Lucía Herrera', '+34 604669914', 'lucía.herrera@example.com', 'Tarea de mantenimiento #81', 'Calle Falsa 86', 'Córdoba', '43275', '46', 'R', 'Carlos Ruiz', '2026-02-09', 'Nota anterior 81', 'Nota posterior 81'),
(82, '75141267E', 'Iván López', '+34 680883936', 'iván.lópez@example.com', 'Tarea de mantenimiento #82', 'Calle Falsa 25', 'Toledo', '19932', '11', 'C', 'Patricia Castro', '2026-02-10', 'Nota anterior 82', 'Nota posterior 82'),
(83, '30194691F', 'Iván López', '+34 612439407', 'iván.lópez@example.com', 'Tarea de mantenimiento #83', 'Calle Falsa 60', 'Málaga', '28246', '46', 'C', 'Patricia Castro', '2026-02-11', 'Nota anterior 83', 'Nota posterior 83'),
(84, '67678201G', 'Laura Gómez', '+34 686711972', 'laura.gómez@example.com', 'Tarea de mantenimiento #84', 'Calle Falsa 61', 'Bilbao', '37880', '21', 'B', 'Patricia Castro', '2026-02-12', 'Nota anterior 84', 'Nota posterior 84'),
(85, '36070053H', 'Iván López', '+34 608748424', 'iván.lópez@example.com', 'Tarea de mantenimiento #85', 'Calle Falsa 14', 'Toledo', '33385', '18', 'B', 'Ana García', '2026-02-13', 'Nota anterior 85', 'Nota posterior 85'),
(86, '19291275I', 'Lucía Herrera', '+34 610648190', 'lucía.herrera@example.com', 'Tarea de mantenimiento #86', 'Calle Falsa 70', 'Vigo', '12870', '41', 'P', 'David López', '2026-02-14', 'Nota anterior 86', 'Nota posterior 86'),
(87, '60448288J', 'Sofía Ruiz', '+34 693214890', 'sofía.ruiz@example.com', 'Tarea de mantenimiento #87', 'Calle Falsa 60', 'Toledo', '20394', '36', 'C', 'Javier Martín', '2026-02-15', 'Nota anterior 87', 'Nota posterior 87'),
(88, '55465152K', 'Raúl Medina', '+34 664925842', 'raúl.medina@example.com', 'Tarea de mantenimiento #88', 'Calle Falsa 10', 'Cádiz', '26244', '21', 'C', 'Ana García', '2026-02-16', 'Nota anterior 88', 'Nota posterior 88'),
(89, '99785754L', 'Sofía Ruiz', '+34 618968130', 'sofía.ruiz@example.com', 'Tarea de mantenimiento #89', 'Calle Falsa 61', 'Granada', '45653', '18', 'R', 'Javier Martín', '2026-02-17', 'Nota anterior 89', 'Nota posterior 89'),
(90, '66982517M', 'Iván López', '+34 610328529', 'iván.lópez@example.com', 'Tarea de mantenimiento #90', 'Calle Falsa 83', 'Granada', '24985', '29', 'C', 'Miguel Sánchez', '2026-02-18', 'Nota anterior 90', 'Nota posterior 90'),
(91, '48071375N', 'Sandra Núñez', '+34 616405171', 'sandra.núñez@example.com', 'Tarea de mantenimiento #91', 'Calle Falsa 48', 'Bilbao', '23537', '46', 'P', 'Javier Martín', '2026-02-19', 'Nota anterior 91', 'Nota posterior 91'),
(92, '43572682O', 'Iván López', '+34 656764090', 'iván.lópez@example.com', 'Tarea de mantenimiento #92', 'Calle Falsa 75', 'Toledo', '30567', '46', 'C', 'Miguel Sánchez', '2026-02-20', 'Nota anterior 92', 'Nota posterior 92'),
(93, '96322003P', 'Pedro Salas', '+34 640601031', 'pedro.salas@example.com', 'Tarea de mantenimiento #93', 'Calle Falsa 55', 'Toledo', '15907', '21', 'C', 'Ana García', '2026-02-21', 'Nota anterior 93', 'Nota posterior 93'),
(94, '73681866Q', 'José Manuel Torres', '+34 610246031', 'josé.manuel.torres@example.com', 'Tarea de mantenimiento #94', 'Calle Falsa 81', 'Toledo', '30351', '36', 'B', 'Miguel Sánchez', '2026-02-22', 'Nota anterior 94', 'Nota posterior 94'),
(95, '27625840R', 'Pedro Salas', '+34 641327665', 'pedro.salas@example.com', 'Tarea de mantenimiento #95', 'Calle Falsa 94', 'Córdoba', '13639', '41', 'B', 'Patricia Castro', '2026-02-23', 'Nota anterior 95', 'Nota posterior 95'),
(96, '93175695S', 'José Manuel Torres', '+34 649747479', 'josé.manuel.torres@example.com', 'Tarea de mantenimiento #96', 'Calle Falsa 7', 'Córdoba', '40016', '45', 'P', 'Miguel Sánchez', '2026-02-24', 'Nota anterior 96', 'Nota posterior 96'),
(97, '48291907T', 'Laura Gómez', '+34 609492403', 'laura.gómez@example.com', 'Tarea de mantenimiento #97', 'Calle Falsa 51', 'Málaga', '11383', '48', 'P', 'Carlos Ruiz', '2026-02-25', 'Nota anterior 97', 'Nota posterior 97'),
(98, '50356982U', 'Raúl Medina', '+34 671944521', 'raúl.medina@example.com', 'Tarea de mantenimiento #98', 'Calle Falsa 44', 'Huelva', '29477', '18', 'C', 'Miguel Sánchez', '2026-02-26', 'Nota anterior 98', 'Nota posterior 98'),
(99, '62615291V', 'Laura Gómez', '+34 617283890', 'laura.gómez@example.com', 'Tarea de mantenimiento #99', 'Calle Falsa 46', 'Huelva', '15392', '41', 'C', 'Miguel Sánchez', '2026-02-27', 'Nota anterior 99', 'Nota posterior 99'),
(100, '54793077W', 'Pedro Salas', '+34 643916014', 'pedro.salas@example.com', 'Tarea de mantenimiento #100', 'Calle Falsa 63', 'Vigo', '28989', '11', 'B', 'Miguel Sánchez', '2026-02-28', 'Nota anterior 100', 'Nota posterior 100'),
(102, '49279924D', 'sofia.ruiz@example.com', '644444444', 'sofia.ruiz@example.com', 'sofia.ruiz@example.com', 'Paseo de Gracia 15', 'Sevilla', '41002', '08', 'R', 'María López', '2025-11-28', 'sofia.ruiz@example.com', ''),
(103, '49279924D', 'sofia.ruiz@example.com', '644444444', 'sofia.ruiz@example.com', 'sofia.ruiz@example.com', 'Paseo de Gracia 15', 'Sevilla', '41002', '08', 'R', 'Juan Pérez', '2025-11-28', 'sofia.ruiz@example.com', ''),
(104, '49279924D', 'sofia.ruiz@example.com', '644444444', 'sofia.ruiz@example.com', 'sofia.ruiz@example.com', 'Paseo de Gracia 15', 'Sevilla', '41002', '08', 'R', '', '2025-11-29', 'NUEVA 2 NOTA POSTERIOR', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `sesion` varchar(255) NOT NULL,
  `guardar_clave` tinyint(1) NOT NULL,
  `rol` varchar(20) NOT NULL DEFAULT 'operario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `contraseña`, `sesion`, `guardar_clave`, `rol`) VALUES
(2, 'admin', 'admin', 'fq7dht9hge82mreu6g0ggedn1q', 0, 'admin'),
(3, 'operario', 'operario', 'lsmh0tvacgf0oo4t7q6tbcv2dk', 0, 'operario'),
(4, 'jmartin', '1234', '7XMCvZkfVMHA2i5xmjF5GdrWHt7iuDPVGKCMan7A', 0, 'operario'),
(5, 'agarcia', '1234', 'QWE987654321PLMKJ', 0, 'operario'),
(6, 'supervisor', 'supervisor', 'SUPERVISORSESSIONTOKEN', 1, 'admin'),
(7, 'rafa', 'rafa123', 'TOKEN123TOKEN123TOKEN123', 0, 'operario');

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
