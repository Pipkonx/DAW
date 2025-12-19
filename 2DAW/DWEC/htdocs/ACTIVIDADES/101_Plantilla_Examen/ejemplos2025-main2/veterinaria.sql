 -- phpMyAdmin SQL Dump 
 -- version 4.8.4 
 -- `https://www.phpmyadmin.net/`  
 -- 
 -- Servidor: localhost:3306 
 -- Tiempo de generación: 14-12-2025 a las 15:46:26 
 -- Versión del servidor: 5.5.62-0ubuntu0.14.04.1 
 -- Versión de PHP: 7.1.14 
  
 SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO"; 
 SET AUTOCOMMIT = 0; 
 START TRANSACTION; 
 SET time_zone = "+00:00"; 
  
  
 /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */; 
 /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */; 
 /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */; 
 /*!40101 SET NAMES utf8mb4 */; 
  
 -- 
 -- Base de datos: `examenclinica` 
 -- 
  
 -- -------------------------------------------------------- 
  
 -- 
 -- Estructura de tabla para la tabla `clientes` 
 -- 
  
 CREATE TABLE `clientes` ( 
   `id` int(11) NOT NULL, 
   `nombre` varchar(100) NOT NULL, 
   `apellidos` varchar(150) NOT NULL, 
   `telefono` varchar(20) NOT NULL, 
   `email` varchar(150) DEFAULT NULL, 
   `direccion` varchar(255) DEFAULT NULL, 
   `fecha_alta` datetime DEFAULT NULL 
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8; 
  
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
 (61, 'Antonio', 'Lorenzo Pardo', '600111020', 'antonio.lorenzo@email.com', 'Calle Camino 16', '2025-12-14 15:40:34'); 
  
 -- -------------------------------------------------------- 
  
 -- 
 -- Estructura de tabla para la tabla `mascotas` 
 -- 
  
 CREATE TABLE `mascotas` ( 
   `id` int(11) NOT NULL, 
   `nombre` varchar(100) NOT NULL, 
   `especie` varchar(50) NOT NULL, 
   `raza` varchar(100) DEFAULT NULL, 
   `fech-nacimiento` date DEFAULT NULL, 
   `id_cliente` int(11) NOT NULL 
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8; 
  
 -- 
 -- Volcado de datos para la tabla `mascotas` 
 -- 
  
 INSERT INTO `mascotas` (`id`, `nombre`, `especie`, `raza`, `fech-nacimiento`, `id_cliente`) VALUES 
 (38, 'Luna', 'Perro', 'Labrador', '2019-05-10', 42), 
 (39, 'Max', 'Perro', 'Pastor Alemán', '2018-03-21', 43), 
 (40, 'Milo', 'Gato', 'Europeo', '2020-07-15', 44), 
 (41, 'Nala', 'Gato', 'Siames', '2021-01-10', 45), 
 (42, 'Rocky', 'Perro', 'Bulldog', '2017-11-02', 46), 
 (43, 'Kira', 'Perro', 'Border Collie', '2022-04-18', 47), 
 (44, 'Simba', 'Gato', 'Maine Coon', '2019-09-09', 48), 
 (45, 'Thor', 'Perro', 'Husky', '2020-12-01', 49), 
 (46, 'Mía', 'Gato', 'Persa', '2021-06-06', 50), 
 (47, 'Toby', 'Perro', 'Beagle', '2018-08-14', 51), 
 (48, 'Leo', 'Gato', 'Europeo', '2019-02-20', 52), 
 (49, 'Coco', 'Perro', 'Caniche', '2020-10-30', 53), 
 (50, 'Bimba', 'Perro', 'Mestizo', '2021-03-11', 54), 
 (51, 'Loki', 'Gato', 'Bengala', '2022-07-01', 55), 
 (52, 'Bruno', 'Perro', 'Rottweiler', '2017-04-05', 56), 
 (53, 'Noa', 'Gato', 'Sphynx', '2021-09-19', 57), 
 (54, 'Dana', 'Perro', 'Golden Retriever', '2018-06-22', 58), 
 (55, 'Olivia', 'Gato', 'Angora', '2020-01-25', 59), 
 (56, 'Zeus', 'Perro', 'Doberman', '2019-11-11', 60), 
 (57, 'Chispa', 'Gato', 'Europeo', '2022-05-03', 61), 
 (58, 'Rex', 'Perro', 'Pastor Belga', '2018-12-12', 42), 
 (59, 'Pelusa', 'Gato', 'Persa', '2020-04-04', 43), 
 (60, 'Nico', 'Perro', 'Mestizo', '2021-08-08', 44), 
 (61, 'Arya', 'Gato', 'Siames', '2019-10-10', 45), 
 (62, 'Tango', 'Perro', 'Bóxer', '2017-07-07', 46), 
 (63, 'Lola', 'Gata', 'Europeo', '2022-02-02', 47), 
 (64, 'Balto', 'Perro', 'Husky', '2018-01-15', 48), 
 (65, 'Kiwi', 'Gato', 'Europeo', '2021-11-21', 49), 
 (66, 'Sombra', 'Gato', 'Bombay', '2020-03-13', 50), 
 (67, 'Duke', 'Perro', 'Gran Danés', '2019-09-09', 51), 
 (68, 'Piolín', 'Ave', 'Canario', '2021-03-15', 52), 
 (69, 'Kiko', 'Ave', 'Periquito', '2022-06-10', 53), 
 (70, 'Lola', 'Ave', 'Agapornis', '2020-09-22', 54), 
 (71, 'Pepe', 'Ave', 'Loro', '2018-01-05', 55), 
 (72, 'Nube', 'Ave', 'Cacatúa', '2019-11-18', 56), 
 (73, 'Draco', 'Reptil', 'Dragón barbudo', '2021-07-07', 