-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-07-2025 a las 23:49:35
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
-- Base de datos: `instituciones`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `instituciones`
--

CREATE TABLE `instituciones` (
  `id` int(11) NOT NULL,
  `institucion` varchar(255) NOT NULL,
  `detalles` text DEFAULT NULL,
  `Tipo_Institucion` text NOT NULL,
  `direccion` text DEFAULT NULL,
  `postal` varchar(200) DEFAULT NULL,
  `banco` varchar(100) DEFAULT NULL,
  `telefono` varchar(200) DEFAULT NULL,
  `Correo` text NOT NULL,
  `ci_rif` varchar(200) DEFAULT NULL,
  `director` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `instituciones`
--

INSERT INTO `instituciones` (`id`, `institucion`, `detalles`, `Tipo_Institucion`, `direccion`, `postal`, `banco`, `telefono`, `Correo`, `ci_rif`, `director`) VALUES
(1, 'A.C. “HERMANOS CONGREGADOS EN EL NOMBRE DEL SEÑOR JESUCRISTO” (Caracas)', 'Asociación de naturaleza civil, con personería jurídica para representar las Asambleas y salvaguardar sus inmuebles', 'Asociación Civil', 'Caracas, 1040 A', NULL, 'Banco Mercantil0105 0015 021015341993', '(0416) 416 20 68 ', 'romerocelisg@hotmail.com. ', 'Rif. J-30912622 Apdo. 40487', 'Abg. Gladys Romero C.'),
(2, 'A.C. “SOCIEDAD DE MAYORDOMOS CRISTIANOS EVANGÉLICOS” (SOCIMACRI) (Puerto Cabello)', 'Asociación de naturaleza civil, con personería jurídica para representar las Asambleas y salvaguardar sus inmuebles', 'Asociación Civil', 'Apdo. 38, Puerto Cabello, Edo. Carabobo, 2024 A', NULL, 'Banco Mercantil 01050073707073034084', '(0416) 416 20 68', 'romerocelisg@hotmail.com.', NULL, 'Samuel Rojas'),
(3, 'COLEGIO EVANGÉLICO DE BARRANCAS (Fundado en 2018) (Barrancas, Estado Barinas)', NULL, 'Colegio Evangélico', 'Pueblo Viejo, Barrancas, Estado Barinas. U.E.P. “Toma de Puerto Cabello” ', NULL, 'Banco Provincial 01082421450100153209', '(0424) 5961373', '', 'C.I. 25.460.790', 'Lourdes García'),
(4, 'COLEGIO EVANGÉLICO DE EL MENE (Fundado en 1936) (El Mene de San Lorenzo, Edo. Falcón)', NULL, 'Colegio Evangélico', 'El Mene de San Lorenzo, Edo. Falcón, 4112', NULL, 'Banesco. 0134 1018 610001000591', '(0416)149 04 64, (0412) 513 19 57', 'colegioevangélicoelmene19@gmail.com', 'RIF: J-08522280-4 ', 'Aleidys Colina de García (Transferir a nombre de A.C. Colegio Evangélico El Mene)'),
(5, 'COLEGIO EVANGÉLICO DE MORÓN (Fundado en 1991) (Colinas de Mara 2, Morón, Edo. Carabobo)', 'U. E. “Colegio Evangélico Sadie de Walmsley”', 'Colegio Evangélico', 'Colinas de Mara 2, Morón, Edo. Carabobo, 2045', NULL, 'Banco Caribe 01140102361020033645', '(0412) 882 49 44, (0412) 943 66 18 ', 'ceswinfo@gmail.com', 'RIF J-30106643-0', 'Marcos Antonio Guzmán Suarez'),
(6, 'COLEGIO EVANGÉLICO DE PUERTO CABELLO (Fundado el 19-05-1919) (Puerto Cabello, Edo. Carabobo)', NULL, 'Colegio Evangélico', 'Calle Santa Barbara, cruce con Campo Elías, No. 11-2, Puerto Cabello, Edo. Carabobo', NULL, 'Transferencia:\r\nBanco Provincial 01080125760100003769\r\nBanesco01340139061393006273\r\nBanca Amiga 0112', '(0414) 580 15 83', 'uecolegioevangelico1919@gmail.com', 'Transferencia:\r\nRIF: J-30297762-2\r\nPago Móvil:\r\nRIF J-30297762-2', 'Lidya Arnías de Piñero '),
(7, 'COLEGIO EVANGÉLICO DE SAN CARLOS (Fundado 13-01-1997) (Los Cerritos, San Carlos, Edo. Cojedes)', 'U. E. “Santiago Saword”', 'Colegio Evangélico', 'Los Cerritos, San Carlos, Edo. Cojedes', NULL, 'Banco Mercantil 01050101601101124903', '(0412) 437 37 00 ', 'E-mail: asociacioncivilfiladelfia@gmail.com', 'RIF J-40105205-3', 'Asociación Civil Filadelfia (Asocifil)'),
(8, 'COLEGIO EVANGÉLICO DE TINAQUILLO (Fundado 15-09-1999) (Tinaquillo, Edo. Cojedes)', 'U. E. “José Turkington” ', 'Colegio Evangélico', 'Prolongación Calle Colina No. 12-155, Urb. Buenos Aires, Tinaquillo, Edo. Cojedes, 2209', NULL, 'Banco de Venezuela 0102 0220550100127379 ', '(0412) 778 45 11 (0414) 163 61 61', 'turkingtonjose@gmail.com.', 'C.I. 13.442.561', 'Amparo de Hernández'),
(9, 'HOGAR EVANGÉLICO PARA ANCIANOS (Asilo) N° 1 (Puerto Cabello, Edo. Carabobo)', '', 'Hogar para Ancianos', 'Calle Principal Ezequiel Zamora,Edificio 41-22, Rancho Grande, Puerto Cabello', 'Apartado 38, Puerto Cabello, Edo. Carabobo, 2024 A', 'Banco Mercantil0105007374107339 6975\r\n\r\nPago Móvil: Banco Mercantil 0105', '(0242) 361 22 87', 'hogarparaancianospc@gmail.com', 'RIF: J-30127639-6', 'José Anciani '),
(10, 'HOGAR EVANGÉLICO PARA ANCIANOS (Asilo) N° 2 (Edo. Miranda)', NULL, 'Hogar para Ancianos', 'Carretera vieja de Charallave a Ocumare del Tuy, en la vía de Mata Linda a Piñate,\r\nSector La Mata Primera, al lado del Dispensario Médico, Edo. Miranda.\r\n', NULL, 'Banco Mercantil 01050015011015324622 ', '(0412) 398 67 45; (0412) 802 14 60', 'hogarlamata@gmail.com', 'RIF: J-30127639-6', 'Hogar Evangélico'),
(11, 'CASA HOGAR PARA NIÑOS: Fundación \"CASA HOGAR DEJAD A LOS NIÑOS VENIR A MI” (Edo. La Guaira)', 'Fundación \"CASA HOGAR DEJAD A LOS NIÑOS \r\nVENIR A MI”\r\n', 'Hogar para Niños', 'Calle Los Molinos, Sector La Gallera, Catia La Mar, Edo. La Guaira', '1162', NULL, '(0412) 470 68 77', '', NULL, 'Luis Acosta y Lidia de Acosta'),
(12, 'HOGAR PARA NIÑOS NECESITADOS: Programa de Apoyo familiar: Casa Hogar EL ROI. (Barrancas, Estado Barinas)', 'Programa de Apoyo familiar', 'Hogar para Niños', 'Casa Hogar EL ROI.\r\nPueblo Viejo, Barrancas, Estado Barinas.', '5201', NULL, '(0424) 5441514', '', NULL, 'Sandra Turkington'),
(13, 'LIBRERÍA Y EDITORIAL LA BUENA SEMILLA C.A. (Palo Negro, Edo. Aragua)', 'Publicación y distribución de tratados y literatura cristiana', 'Editorial', 'Calle 5, Casa 93, Urb. El Orticeño, Palo Negro, Edo. Aragua, 2117', NULL, 'Banco Mercantil 01050190391190011336', '(0243) 267 42 78, (0414) 460 29 12', 'lyelbs@yahoo.com', 'RIF: J-30581094-0', 'Samuel Ussher (h)'),
(14, 'EDITORIAL “LA SANA DOCTRINA”', 'Revista Digital \r\n\r\nPágina Web: https://sanadoctrina.net\r\n\r\nTelegram: Revista \"La Sana Doctrina\"', 'Editorial', NULL, NULL, NULL, '(0424) 414 98 56', 'revistasanadoctrina@gmail.com', NULL, 'Andrew Turkington'),
(15, 'EDITORIAL “LA VOZ EN EL DESIERTO”', 'Web: www.entregandoelpan.com/lavoz/', 'Editorial', NULL, NULL, 'Banco Mercantil 01050015060015313166 ', '(0424) 257 63 21 - (0414) 211 62 49 - (0424) 231 67 84', 'lvdesierto@gmail.com, lavozeneldesierto@entregandoelpan.com', NULL, 'Carlos Sequera'),
(16, 'REVISTA “ENTREGANDO EL PAN”', 'Se distribuye por internet en formato pdf\r\n\r\nweb: www.entregandoelpan.com', 'Editorial', NULL, NULL, 'Banco Mercantil 01050015060015313166 ', '(0212) 631 57 96 - (0416) 210 08 06', 'revista@entregandoelpan.com', NULL, 'Carlos Sequera');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `instituciones`
--
ALTER TABLE `instituciones`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `instituciones`
--
ALTER TABLE `instituciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
