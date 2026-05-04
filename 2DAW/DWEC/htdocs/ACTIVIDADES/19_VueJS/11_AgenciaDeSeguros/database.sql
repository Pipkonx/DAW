-- Base de datos para Agencia de Seguros Aeterna
-- Versión con municipios para todas las provincias
CREATE DATABASE IF NOT EXISTS `aeternaseguros` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `aeternaseguros`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `provincias`
--

CREATE TABLE `provincias` (
  `id` int(11) NOT NULL,
  `provincia` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `provincias`
--

INSERT INTO `provincias` (`id`, `provincia`) VALUES
(1, 'Álava'), (2, 'Albacete'), (3, 'Alicante'), (4, 'Almería'), (5, 'Ávila'),
(6, 'Badajoz'), (7, 'Baleares'), (8, 'Barcelona'), (9, 'Burgos'), (10, 'Cáceres'),
(11, 'Cádiz'), (12, 'Castellón'), (13, 'Ciudad Real'), (14, 'Córdoba'), (15, 'A Coruña'),
(16, 'Cuenca'), (17, 'Girona'), (18, 'Granada'), (19, 'Guadalajara'), (20, 'Gipuzkoa'),
(21, 'Huelva'), (22, 'Huesca'), (23, 'Jaén'), (24, 'León'), (25, 'Lleida'),
(26, 'La Rioja'), (27, 'Lugo'), (28, 'Madrid'), (29, 'Málaga'), (30, 'Murcia'),
(31, 'Navarra'), (32, 'Ourense'), (33, 'Asturias'), (34, 'Palencia'), (35, 'Las Palmas'),
(36, 'Pontevedra'), (37, 'Salamanca'), (38, 'Santa Cruz de Tenerife'), (39, 'Cantabria'), (40, 'Segovia'),
(41, 'Sevilla'), (42, 'Soria'), (43, 'Tarragona'), (44, 'Teruel'), (45, 'Toledo'),
(46, 'Valencia'), (47, 'Valladolid'), (48, 'Bizkaia'), (49, 'Zamora'), (50, 'Zaragoza'),
(51, 'Ceuta'), (52, 'Melilla');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `municipios`
--

CREATE TABLE `municipios` (
  `id` int(11) NOT NULL,
  `municipio` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `municipios` (Muestra representativa para todas las provincias)
--

INSERT INTO `municipios` (`id`, `municipio`) VALUES
-- Álava (1)
(1001, 'Alegría-Dulantzi'), (1002, 'Amurrio'), (1059, 'Vitoria-Gasteiz'),
-- Albacete (2)
(2001, 'Abengibre'), (2002, 'Alatoz'), (2003, 'Albacete'), (2037, 'Hellín'),
-- Alicante (3)
(3001, 'Alicante/Alacant'), (3002, 'Elche/Elx'), (3003, 'Dénia'), (3004, 'Benidorm'), (3065, 'Elda'),
-- Almería (4)
(4001, 'Abla'), (4002, 'Abrucena'), (4013, 'Almería'), (4032, 'Ejido, El'), (4079, 'Roquetas de Mar'),
-- Ávila (5)
(5001, 'Adanero'), (5002, 'Adrada, La'), (5019, 'Ávila'),
-- Badajoz (6)
(6001, 'Acedera'), (6015, 'Badajoz'), (6083, 'Mérida'), (6158, 'Zafra'),
-- Baleares (7)
(7001, 'Alaior'), (7040, 'Palma de Mallorca'), (7026, 'Ibiza'), (7032, 'Mahón'),
-- Barcelona (8)
(8001, 'Abrera'), (8015, 'Badalona'), (8019, 'Barcelona'), (8101, 'Hospitalet de Llobregat'), (8279, 'Terrassa'),
-- Burgos (9)
(9001, 'Abajas'), (9059, 'Burgos'), (9023, 'Aranda de Duero'), (9080, 'Miranda de Ebro'),
-- Cáceres (10)
(10001, 'Abadía'), (10037, 'Cáceres'), (10148, 'Plasencia'),
-- Cádiz (11)
(11001, 'Alcalá de los Gazules'), (11012, 'Cádiz'), (11020, 'Jerez de la Frontera'), (11004, 'Algeciras'),
-- Castellón (12)
(12001, 'Atzeneta del Maestrat'), (12040, 'Castellón de la Plana'), (12135, 'Vila-real'),
-- Ciudad Real (13)
(13001, 'Abenójar'), (13034, 'Ciudad Real'), (13071, 'Puertollano'), (13087, 'Valdepeñas'),
-- Córdoba (14)
(14001, 'Adamuz'), (14021, 'Córdoba'), (14038, 'Lucena'), (14056, 'Puente Genil'),
-- A Coruña (15)
(15001, 'Abegondo'), (15030, 'Coruña, A'), (15078, 'Santiago de Compostela'), (15036, 'Ferrol'),
-- Cuenca (16)
(16001, 'Abia de la Obispalía'), (16078, 'Cuenca'), (16202, 'Tarancón'),
-- Girona (17)
(17001, 'Agullana'), (17079, 'Girona'), (17066, 'Figueres'), (17102, 'Lloret de Mar'),
-- Granada (18)
(18001, 'Agrón'), (18087, 'Granada'), (18140, 'Motril'), (18003, 'Almuñécar'),
-- Guadalajara (19)
(19001, 'Abánades'), (19130, 'Guadalajara'), (19024, 'Azuqueca de Henares'),
-- Gipuzkoa (20)
(20001, 'Abaltzisketa'), (20069, 'Donostia-San Sebastián'), (20045, 'Irun'), (20030, 'Eibar'),
-- Huelva (21)
(21001, 'Alájar'), (21041, 'Huelva'), (21005, 'Almonte'), (21044, 'Lepe'),
-- Huesca (22)
(22001, 'Abiego'), (22125, 'Huesca'), (22048, 'Barbastro'), (22158, 'Monzón'),
-- Jaén (23)
(23001, 'Albanchez de Mágina'), (23050, 'Jaén'), (23055, 'Linares'), (23092, 'Úbeda'),
-- León (24)
(24001, 'Acebedo'), (24089, 'León'), (24115, 'Ponferrada'), (24008, 'Astorga'),
-- Lleida (25)
(25001, 'Abella de la Conca'), (25120, 'Lleida'), (25038, 'Balaguer'),
-- La Rioja (26)
(26001, 'Ábalos'), (26089, 'Logroño'), (26036, 'Calahorra'),
-- Lugo (27)
(27001, 'Abadín'), (27028, 'Lugo'), (27031, 'Monforte de Lemos'),
-- Madrid (28)
(28001, 'Acebeda, La'), (28079, 'Madrid'), (28003, 'Alcalá de Henares'), (28092, 'Móstoles'), (28065, 'Getafe'),
-- Málaga (29)
(29001, 'Alameda'), (29067, 'Málaga'), (29069, 'Marbella'), (29094, 'Vélez-Málaga'), (29054, 'Fuengirola'),
-- Murcia (30)
(30001, 'Abanilla'), (30030, 'Murcia'), (30016, 'Cartagena'), (30024, 'Lorca'),
-- Navarra (31)
(31001, 'Abáigar'), (31201, 'Pamplona/Iruña'), (31232, 'Tudela'),
-- Ourense (32)
(32001, 'Allariz'), (32054, 'Ourense'), (32089, 'Verín'),
-- Asturias (33)
(33001, 'Allande'), (33044, 'Oviedo'), (33024, 'Gijón'), (33004, 'Avilés'),
-- Palencia (34)
(34001, 'Abarca de Campos'), (34120, 'Palencia'),
-- Las Palmas (35)
(35001, 'Agaete'), (35016, 'Las Palmas de Gran Canaria'), (35004, 'Arrecife'), (35017, 'Puerto del Rosario'),
-- Pontevedra (36)
(36001, 'Agolada'), (36038, 'Pontevedra'), (36057, 'Vigo'),
-- Salamanca (37)
(37001, 'Abusejo'), (37274, 'Salamanca'),
-- Santa Cruz de Tenerife (38)
(38001, 'Adeje'), (38038, 'Santa Cruz de Tenerife'), (38023, 'San Cristóbal de La Laguna'), (38006, 'Arona'),
-- Cantabria (39)
(39001, 'Alfoz de Lloredo'), (39075, 'Santander'), (39087, 'Torrelavega'),
-- Segovia (40)
(40001, 'Abades'), (40194, 'Segovia'),
-- Sevilla (41)
(41001, 'Aguadulce'), (41091, 'Sevilla'), (41038, 'Dos Hermanas'), (41004, 'Alcalá de Guadaíra'),
-- Soria (42)
(42001, 'Abejar'), (42173, 'Soria'),
-- Tarragona (43)
(43001, 'Aiguamúrcia'), (43148, 'Tarragona'), (43123, 'Reus'), (43155, 'Tortosa'),
-- Teruel (44)
(44001, 'Ababuj'), (44216, 'Teruel'), (44013, 'Alcañiz'),
-- Toledo (45)
(45001, 'Ajofrín'), (45168, 'Toledo'), (45165, 'Talavera de la Reina'),
-- Valencia (46)
(46001, 'Ademuz'), (46250, 'Valencia'), (46220, 'Torrent'), (46131, 'Gandia'),
-- Valladolid (47)
(47001, 'Adalia'), (47186, 'Valladolid'), (47085, 'Medina del Campo'),
-- Bizkaia (48)
(48001, 'Abadiño'), (48020, 'Bilbao'), (48013, 'Barakaldo'), (48044, 'Getxo'),
-- Zamora (49)
(49001, 'Abezames'), (49275, 'Zamora'), (49021, 'Benavente'),
-- Zaragoza (50)
(50001, 'Abanto'), (50297, 'Zaragoza'), (50067, 'Calatayud'),
-- Ceuta (51)
(51001, 'Ceuta'),
-- Melilla (52)
(52001, 'Melilla');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios` (Asegurados)
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `tlf` varchar(20) DEFAULT NULL,
  `localidad` int(11) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `provincia` int(11) DEFAULT NULL,
  `tipo` tinyint(1) DEFAULT 0, -- 0: Particular, 1: Empresa
  `contrasena` varchar(100) DEFAULT NULL,
  `login` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellidos`, `tlf`, `localidad`, `cp`, `provincia`, `tipo`, `contrasena`, `login`) VALUES
(1, 'Rafael', 'García López', '600111222', 28079, '28001', 28, 0, '1234', 'rafael'),
(2, 'Seguros Levante S.L.', 'Empresa de Transportes', '965000000', 3001, '03001', 3, 1, 'admin', 'levante'),
(3, 'María', 'Sánchez Pérez', '611222333', 41091, '41001', 41, 0, 'maria123', 'maria'),
(4, 'Taller Mecánico Juan', 'Rodríguez Martínez', '933444555', 8019, '08001', 8, 1, 'taller', 'tallerjuan');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `polizas`
--

CREATE TABLE `polizas` (
  `idPoliza` int(11) NOT NULL AUTO_INCREMENT,
  `idUsuario` int(11) NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `estado` int(11) DEFAULT 0, -- 0: cobrado, 1: a cuenta, 2: liquidada, 3: anulada, 4: pre-anulada
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`idPoliza`),
  KEY `idUsuario` (`idUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `polizas`
--

INSERT INTO `polizas` (`idPoliza`, `idUsuario`, `importe`, `fecha`, `estado`, `observaciones`) VALUES
(1, 1, 450.00, '2024-01-15', 0, 'Seguro de coche anual'),
(2, 1, 120.00, '2024-03-10', 1, 'Seguro de hogar trimestral'),
(3, 2, 2500.50, '2024-02-20', 2, 'Flota de vehículos industriales'),
(4, 3, 85.00, '2024-05-01', 0, 'Seguro de vida');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `idPago` int(11) NOT NULL AUTO_INCREMENT,
  `idPoliza` int(11) NOT NULL,
  `importe` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  PRIMARY KEY (`idPago`),
  KEY `idPoliza` (`idPoliza`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pagos`
--

INSERT INTO `pagos` (`idPago`, `idPoliza`, `importe`, `fecha`) VALUES
(1, 1, 450.00, '2024-01-15'),
(2, 2, 60.00, '2024-03-10'),
(3, 3, 1000.00, '2024-02-25'),
(4, 3, 1500.50, '2024-03-15'),
(5, 4, 85.00, '2024-05-01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logins` (Usuarios del sistema / Empleados)
--

CREATE TABLE `logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `tipo` int(11) DEFAULT 1, -- 0: Admin, 1: Usuario/Empleado
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `logins`
--

INSERT INTO `logins` (`id`, `nombre`, `password`, `tipo`) VALUES
(1, 'admin', 'admin', 0),
(2, 'empleado1', '1234', 1),
(3, 'gestor', 'gestor', 1);

-- --------------------------------------------------------

--
-- Restricciones para tablas volcadas
--

-- Filtros para la tabla `polizas`
ALTER TABLE `polizas`
  ADD CONSTRAINT `polizas_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

-- Filtros para la tabla `pagos`
ALTER TABLE `pagos`
  ADD CONSTRAINT `pagos_ibfk_1` FOREIGN KEY (`idPoliza`) REFERENCES `polizas` (`idPoliza`) ON DELETE CASCADE;