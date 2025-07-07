-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-07-2025 a las 23:47:35
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
-- Base de datos: `chat_biblico`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `sala` varchar(100) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `mensaje` text DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`id`, `sala`, `nombre`, `mensaje`, `fecha`) VALUES
(3, 'La Oración', 'john Malavé', 'Cual es la importancia de la oración para la vida del Cristiano?', '2025-05-27 09:16:02'),
(4, 'La Oración', '', 'como se debe orar?', '2025-06-09 15:03:23'),
(5, 'La Oración', 'Invitado_3890', 'Con temor de Dios', '2025-06-12 16:32:02'),
(8, 'La Oración', 'Invitado_3890', 'hola', '2025-06-12 16:33:31'),
(11, 'La Oración', 'Invitado_8073', 'hola', '2025-06-12 17:32:07'),
(12, 'La Oración', 'Invitado_8073', 's', '2025-06-12 17:36:27'),
(22, 'La Oración', 'Invitado_2455', 'ds', '2025-06-13 10:38:07'),
(23, 'La Oración', 'Invitado_2455', 'ds', '2025-06-13 10:38:10'),
(25, 'Estudio Bíblico', 'Invitado_2455', 'es importante orar?', '2025-06-13 11:16:15'),
(26, 'Estudio Bíblico', 'John Malavé', 'hola', '2025-06-13 11:17:39'),
(32, 'Estudio Bíblico', 'John Malavé', 'd', '2025-06-25 15:46:47'),
(33, 'Estudio Bíblico', 'John Malavé', 'd', '2025-06-25 15:46:50'),
(34, 'Estudio Bíblico', 'John Malavé', 'd', '2025-06-25 15:46:54'),
(35, 'Estudio Bíblico', 'John Malavé', 'd', '2025-06-25 15:46:56'),
(36, 'Estudio Bíblico', 'John Malavé', 'd', '2025-06-25 15:46:58'),
(37, 'Estudio Bíblico', 'John Malavé', 'd', '2025-06-25 15:47:12'),
(38, 'Estudio Bíblico', 'Lois Mckeown', 'hola\\r\\n', '2025-06-25 17:05:57'),
(39, 'Estudio Bíblico', 'Lois Mckeown', 'cual es tu pregunta?', '2025-06-25 17:06:30'),
(40, 'Estudio Bíblico', 'John Malavé', 'si', '2025-06-26 10:56:08'),
(41, 'La Oración', 'John Malavé', 'ds', '2025-06-28 13:04:07'),
(42, 'La Oración', 'John Malavé', 'ds', '2025-06-28 17:30:54'),
(43, 'La Oración', 'John Malavé', 'ds', '2025-06-28 17:31:08'),
(44, 'La Oración', 'John Malavé', 'a', '2025-06-28 17:31:21'),
(45, 'La Oración', 'John Malavé', 's', '2025-06-28 17:31:24'),
(46, 'La Oración', 'John Malavé', 's', '2025-06-28 17:32:56'),
(47, 'La Oración', 'John Malavé', 'ew\\r\\n', '2025-06-28 17:46:05'),
(48, 'La Oración', 'John Malavé', 'ew\\r\\n', '2025-06-28 17:47:26'),
(49, 'La Oración', 'Admin', 'hola', '2025-06-28 18:26:52'),
(50, 'La Oración', 'Admin', 'hola', '2025-06-28 18:27:28'),
(51, 'Estudio Bíblico', 'Admin', 'hola\\r\\n\\r\\nesto es un mensaje', '2025-06-28 18:36:43'),
(52, 'Estudio Bíblico', 'Admin', 'hola\\r\\n\\r\\nesto es un mensaje', '2025-06-28 18:40:32'),
(53, 'Estudio Bíblico', 'Admin', 'ds\\r\\n\\r\\nds', '2025-06-28 18:40:38'),
(54, 'Estudio Bíblico', 'Admin', 'ds\\r\\nsd', '2025-06-28 18:40:48'),
(55, 'Estudio Bíblico', 'Lois Mckeown', 'Como debemos estudia la Biblia para poder aprender mas y estar mas cerca del Señor?', '2025-06-30 16:44:39'),
(56, 'La Oración', 'John Malavé', '¿Cual es la importancia de la Oración para la vida diaria del Cristiano?', '2025-06-30 20:37:03');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
