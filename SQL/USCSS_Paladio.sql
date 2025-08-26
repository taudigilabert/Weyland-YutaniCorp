-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-04-2025 a las 15:54:07
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `uscss_paladio`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado_autoincremento`
--

CREATE TABLE `empleado_autoincremento` (
  `id` int(11) NOT NULL,
  `numero_empleado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleado_autoincremento`
--

INSERT INTO `empleado_autoincremento` (`id`, `numero_empleado`) VALUES
(1, 346441);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informes_usuario`
--

CREATE TABLE `informes_usuario` (
  `inf_id` int(11) NOT NULL,
  `usu_id` int(11) NOT NULL,
  `inf_concepto` varchar(500) NOT NULL,
  `inf_contenido` text NOT NULL,
  `inf_fecha` date NOT NULL,
  `inf_estado` enum('abierto','archivado') DEFAULT 'abierto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `informe_imagenes`
--

CREATE TABLE `informe_imagenes` (
  `img_id` int(11) NOT NULL,
  `inf_id` int(11) NOT NULL,
  `img_ruta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `men_id` int(11) NOT NULL,
  `men_asunto` varchar(255) DEFAULT 'Mensaje sin asunto',
  `men_remitente` int(11) NOT NULL,
  `men_contenido` text DEFAULT NULL,
  `men_fecha` datetime DEFAULT current_timestamp(),
  `rol_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensajes`
--

INSERT INTO `mensajes` (`men_id`, `men_asunto`, `men_remitente`, `men_contenido`, `men_fecha`, `rol_id`) VALUES
(29, 'Hola', 21, 'Hola!', '2025-04-23 15:47:12', NULL),
(30, 'RE: Hola', 19, 'Hola Tomas!', '2025-04-23 15:49:04', NULL),
(31, 'RE: Hola', 19, 'Hola!', '2025-04-23 15:50:24', NULL),
(32, 'RE: Hola', 21, 'Hola de nuevo!', '2025-04-23 15:50:52', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes_imagenes`
--

CREATE TABLE `mensajes_imagenes` (
  `mimg_id` int(11) NOT NULL,
  `men_id` int(11) NOT NULL,
  `mimg_ruta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensaje_receptores`
--

CREATE TABLE `mensaje_receptores` (
  `mec_id` int(11) NOT NULL,
  `men_id` int(11) NOT NULL,
  `mec_receptor` int(11) NOT NULL,
  `mec_leido` tinyint(1) DEFAULT 0,
  `mec_notificacion` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mensaje_receptores`
--

INSERT INTO `mensaje_receptores` (`mec_id`, `men_id`, `mec_receptor`, `mec_leido`, `mec_notificacion`) VALUES
(33, 29, 19, 0, 0),
(34, 30, 21, 0, 0),
(35, 31, 21, 0, 1),
(36, 32, 19, 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nave`
--

CREATE TABLE `nave` (
  `nav_id` int(11) NOT NULL,
  `nav_nombre` varchar(100) NOT NULL,
  `nav_tipo` varchar(50) DEFAULT NULL,
  `nav_descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `nave`
--

INSERT INTO `nave` (`nav_id`, `nav_nombre`, `nav_tipo`, `nav_descripcion`) VALUES
(1, 'USCSS Paladio', 'Investigación', 'Nave utilizada para experimentos biológicos y operaciones científicas.'),
(2, 'USCSS Nostromo', 'Carguero', 'Nave comercial dedicada al transporte de carga interestelar.'),
(3, 'USS Sulaco', 'Militar', 'Fragata de combate clase Conestoga, utilizada para operaciones militares.'),
(4, 'USCSS Prometheus', 'Exploración', 'Nave de exploración científica avanzada de Weyland Corp.'),
(5, 'USM Auriga', 'Investigación', 'Nave militar utilizada para experimentos biológicos.'),
(6, 'Sevastopol', 'Estación Espacial', 'Estación orbital equipada para comercio y soporte de vida prolongado.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_imagenes`
--

CREATE TABLE `registro_imagenes` (
  `rei_id` int(11) NOT NULL,
  `rem_id` int(11) NOT NULL,
  `rei_ruta_imagen` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registro_imagenes`
--

INSERT INTO `registro_imagenes` (`rei_id`, `rem_id`, `rei_ruta_imagen`) VALUES
(2, 2, 'img/registros/primer_oficial.jpg'),
(3, 3, 'img/registros/personal.jpg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_medico`
--

CREATE TABLE `registro_medico` (
  `rem_id` int(11) NOT NULL,
  `usu_id_medico` int(11) NOT NULL,
  `usu_id_paciente` int(11) NOT NULL,
  `rem_fecha` date NOT NULL,
  `rem_contenido` text NOT NULL,
  `rem_actualizado` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registro_medico`
--

INSERT INTO `registro_medico` (`rem_id`, `usu_id_medico`, `usu_id_paciente`, `rem_fecha`, `rem_contenido`, `rem_actualizado`) VALUES
(2, 3, 2, '2025-01-15', 'Revisión médica del primer oficial. Sin anomalías detectadas.', '2025-01-23 17:25:03'),
(3, 3, 3, '2025-01-20', 'Informe médico de autogenerado para seguimiento personal.', '2025-01-23 17:25:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_movimientos`
--

CREATE TABLE `registro_movimientos` (
  `reg_id` int(11) NOT NULL,
  `usu_id` int(11) NOT NULL,
  `reg_accion` varchar(100) NOT NULL,
  `reg_fecha` date NOT NULL,
  `reg_hora` time NOT NULL,
  `men_id` int(11) DEFAULT NULL,
  `inf_id` int(11) DEFAULT NULL,
  `rem_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registro_movimientos`
--

INSERT INTO `registro_movimientos` (`reg_id`, `usu_id`, `reg_accion`, `reg_fecha`, `reg_hora`, `men_id`, `inf_id`, `rem_id`) VALUES
(2, 2, 'Subió informe de análisis', '2025-01-15', '12:30:00', NULL, NULL, NULL),
(3, 3, 'Actualizó registro médico', '2025-01-20', '15:00:00', NULL, NULL, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `rol_id` int(11) NOT NULL,
  `rol_nombre` varchar(100) NOT NULL,
  `rol_descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`rol_id`, `rol_nombre`, `rol_descripcion`) VALUES
(1, 'Capitán', 'Líder de la tripulación, responsable de todas las operaciones a bordo.'),
(2, 'Primer oficial', 'Segundo al mando, apoya al capitán en las operaciones.'),
(3, 'Ingeniero jefe', 'Responsable del mantenimiento y operación de los sistemas de la nave.'),
(4, 'Oficial de seguridad', 'Encargado de la seguridad de la nave y la tripulación.'),
(5, 'Ingeniero de mantenimiento', 'Técnico responsable del mantenimiento de sistemas y equipo.'),
(6, 'Piloto', 'Encargado de la navegación y control de la nave en vuelo.'),
(7, 'Médico oficial', 'Responsable de la salud de la tripulación y del manejo de registros médicos.'),
(8, 'Científico principal', 'Líder de la investigación científica a bordo.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `usu_id` int(11) NOT NULL,
  `usu_nombre` varchar(100) NOT NULL,
  `usu_apellido` varchar(100) NOT NULL,
  `usu_alias` varchar(100) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `usu_genero` enum('Masculino','Femenino','Otro') DEFAULT NULL,
  `usu_biografia` text DEFAULT NULL,
  `usu_imagen` varchar(100) DEFAULT 'userDefault.jpg',
  `usu_idnave` int(11) DEFAULT 1,
  `usu_numero_empleado` int(11) NOT NULL,
  `usu_fecha_creacion` datetime DEFAULT current_timestamp(),
  `usu_contrasena` varchar(255) NOT NULL,
  `usu_activo` tinyint(1) DEFAULT 1,
  `usu_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`usu_id`, `usu_nombre`, `usu_apellido`, `usu_alias`, `rol_id`, `usu_genero`, `usu_biografia`, `usu_imagen`, `usu_idnave`, `usu_numero_empleado`, `usu_fecha_creacion`, `usu_contrasena`, `usu_activo`, `usu_token`) VALUES
(2, 'Spock', 'Sarek', 'primer_spock', 2, 'Masculino', 'Primer oficial lógico y analítico, fundamental para la misión.', 'img/usuarios/spock.jpg', 1, 5677, '2025-01-23 17:24:01', '123', 1, NULL),
(3, 'Leonard', 'McCoy', 'doc_mccoy', 7, 'Masculino', 'Oficial médico responsable de la salud de la tripulación.', 'img/usuarios/mccoy.jpg', 1, 874545, '2025-01-23 17:24:01', '123', 0, NULL),
(19, 'Mary', 'Ramirez', 'maryramirez', 1, 'Femenino', 'Me gustan los gatos!', 'fotoPerfil_6807d2df1e8ff3.75500118.jpg', 1, 346436, '2025-04-22 19:33:19', '$2y$10$2SUqcmB7FOWwD3xIU5kcEeE2WwTm20R7ZP1rEWOT2GiLl1w/1vxSq', 1, 'e0603ac0258df19b1734cca8c28ea3204b8700bc10d05f708dcedf4deb5c834c'),
(20, 'Arthur', 'Dallas', 'arthurD', 1, 'Masculino', 'Nací en un tubo de acero llamado Theophilus Station (Luna Sector 7), donde la gravedad es un chiste malo y el aire huele a filtros reciclados. Mi padre era ingeniero de motores FTL, mi madre una de esas \"científicas corporativas\" que firmaban informes sin leerlos. A los 12 años ya sabía pilotar un transbordador de carga mejor que los adultos borrachos de la estación.', 'fotoPerfil_6807d47a1b2c83.80238773.jpg', 1, 346437, '2025-04-22 19:40:10', '$2y$10$QU8Vw8/3YDJFhfKPbKDEkuLwW5go5AzepzsAGqDDQqSFlWeyHXJ02', 1, '81d7f6785ce856234ef7c7b22883f3fba722b09ff5759b00e6d8811f55837ad5'),
(21, 'Tomás', 'Audi', 'TomasAudi', 1, 'Masculino', 'a', 'fotoPerfil_6807d50091c602.44058311.jpg', 1, 346438, '2025-04-22 19:42:24', '$2y$10$Z2b2LtXa5TAT6P4.nUGiQemm4FMHvHLGkGHOBvWbAr/GwtSEzjiMO', 1, '3b597a15e5e674ede7568491bc9729e69118e49ea767f7bafaba14e4ce3de603'),
(22, 'Ash', 'Weyland', 'AshW', 3, 'Masculino', 'Permítanme ser claro: no soy un mero \'robot\'. Soy la culminación del genio humano... con mejoras obvias. Mi programación prioriza dos objetivos:\r\n\r\nPreservar el activo biológico a toda costa\r\n\r\nAsegurar que la Compañía obtenga ROI en I+D... incluso si eso requiere... redefinir \'ética\'\r\n\r\n¿Biografía? Mis \'recuerdos\' incluyen 7 idiomas, 14 protocolos de vivisección y el manual completo de navegación del Nostromo. Pero mi favorito es el día que entendí la belleza del xenomorfo: perfecto, letal... rentable.', 'fotoPerfil_6807d5a4b34f54.50775875.jpg', 1, 346439, '2025-04-22 19:45:08', '$2y$10$FkUiPTGHzVcALOhO95mAReTj5GXqOiTsZjOzvwge7EvsYhUg/Anrq', 1, '6e34d701fe82bbdd668bfda80aa0085c4656dd40cc5c1a5ef48ae589065adb7f'),
(23, 'Dennis', 'Parker', 'DennisP', 4, 'Masculino', 'Si estás leyendo esto, o eres PsicOcupacional o me debes dinero. Aquí va mi \'historia oficial\':\r\n\r\nNací entre tubos de oxígeno y sobornos marcianos. Mi padre trabajaba en Comms para la Weyland-Yutani y mi madre vendía \"oxígeno premium\" en el mercado negro. Aprendí a piratear canales de transmisión antes que a atarme los zapatos.', 'fotoPerfil_6807d63be4c567.26674675.jpg', 1, 346440, '2025-04-22 19:47:39', '$2y$10$6o//IACmcKEF8XKCiELGUehbxbxC.qNh.1YcuNaz3C/83j7M/HYJO', 1, 'f865ea49e402e88865d85cb4e904748d1ec047d5264c1ae48f590de04d401548');

--
-- Disparadores `usuarios`
--
DELIMITER $$
CREATE TRIGGER `antes_insertar_usuario` BEFORE INSERT ON `usuarios` FOR EACH ROW BEGIN
    DECLARE nuevo_numero INT;

    -- Obtener el próximo número de empleado
    SELECT numero_empleado INTO nuevo_numero
    FROM empleado_autoincremento
    ORDER BY id DESC
    LIMIT 1;

    -- Incrementar el valor en la tabla auxiliar
    UPDATE empleado_autoincremento
    SET numero_empleado = numero_empleado + 1
    ORDER BY id DESC
    LIMIT 1;

    -- Asignar el número al nuevo usuario
    SET NEW.usu_numero_empleado = nuevo_numero;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `empleado_autoincremento`
--
ALTER TABLE `empleado_autoincremento`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `informes_usuario`
--
ALTER TABLE `informes_usuario`
  ADD PRIMARY KEY (`inf_id`),
  ADD KEY `usu_id` (`usu_id`),
  ADD KEY `idx_informe_fecha` (`inf_fecha`);

--
-- Indices de la tabla `informe_imagenes`
--
ALTER TABLE `informe_imagenes`
  ADD PRIMARY KEY (`img_id`),
  ADD KEY `inf_id` (`inf_id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`men_id`),
  ADD KEY `men_remitente` (`men_remitente`),
  ADD KEY `rol_id` (`rol_id`);

--
-- Indices de la tabla `mensajes_imagenes`
--
ALTER TABLE `mensajes_imagenes`
  ADD PRIMARY KEY (`mimg_id`),
  ADD KEY `men_id` (`men_id`);

--
-- Indices de la tabla `mensaje_receptores`
--
ALTER TABLE `mensaje_receptores`
  ADD PRIMARY KEY (`mec_id`),
  ADD KEY `men_id` (`men_id`),
  ADD KEY `mec_receptor` (`mec_receptor`);

--
-- Indices de la tabla `nave`
--
ALTER TABLE `nave`
  ADD PRIMARY KEY (`nav_id`);

--
-- Indices de la tabla `registro_imagenes`
--
ALTER TABLE `registro_imagenes`
  ADD PRIMARY KEY (`rei_id`),
  ADD KEY `rem_id` (`rem_id`);

--
-- Indices de la tabla `registro_medico`
--
ALTER TABLE `registro_medico`
  ADD PRIMARY KEY (`rem_id`),
  ADD KEY `usu_id_medico` (`usu_id_medico`),
  ADD KEY `usu_id_paciente` (`usu_id_paciente`);

--
-- Indices de la tabla `registro_movimientos`
--
ALTER TABLE `registro_movimientos`
  ADD PRIMARY KEY (`reg_id`),
  ADD KEY `usu_id` (`usu_id`),
  ADD KEY `men_id` (`men_id`),
  ADD KEY `inf_id` (`inf_id`),
  ADD KEY `rem_id` (`rem_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`rol_id`),
  ADD UNIQUE KEY `rol_nombre` (`rol_nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usu_id`),
  ADD UNIQUE KEY `usu_alias` (`usu_alias`),
  ADD UNIQUE KEY `usu_numero_empleado` (`usu_numero_empleado`),
  ADD KEY `rol_id` (`rol_id`),
  ADD KEY `usu_idnave` (`usu_idnave`),
  ADD KEY `idx_usuario_alias` (`usu_alias`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleado_autoincremento`
--
ALTER TABLE `empleado_autoincremento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `informes_usuario`
--
ALTER TABLE `informes_usuario`
  MODIFY `inf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `informe_imagenes`
--
ALTER TABLE `informe_imagenes`
  MODIFY `img_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `men_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `mensajes_imagenes`
--
ALTER TABLE `mensajes_imagenes`
  MODIFY `mimg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `mensaje_receptores`
--
ALTER TABLE `mensaje_receptores`
  MODIFY `mec_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de la tabla `nave`
--
ALTER TABLE `nave`
  MODIFY `nav_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `registro_imagenes`
--
ALTER TABLE `registro_imagenes`
  MODIFY `rei_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `registro_medico`
--
ALTER TABLE `registro_medico`
  MODIFY `rem_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `registro_movimientos`
--
ALTER TABLE `registro_movimientos`
  MODIFY `reg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `rol_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `informes_usuario`
--
ALTER TABLE `informes_usuario`
  ADD CONSTRAINT `informes_usuario_ibfk_1` FOREIGN KEY (`usu_id`) REFERENCES `usuarios` (`usu_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `informe_imagenes`
--
ALTER TABLE `informe_imagenes`
  ADD CONSTRAINT `informe_imagenes_ibfk_1` FOREIGN KEY (`inf_id`) REFERENCES `informes_usuario` (`inf_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`men_remitente`) REFERENCES `usuarios` (`usu_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`rol_id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `mensajes_imagenes`
--
ALTER TABLE `mensajes_imagenes`
  ADD CONSTRAINT `mensajes_imagenes_ibfk_1` FOREIGN KEY (`men_id`) REFERENCES `mensajes` (`men_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mensaje_receptores`
--
ALTER TABLE `mensaje_receptores`
  ADD CONSTRAINT `mensaje_receptores_ibfk_1` FOREIGN KEY (`men_id`) REFERENCES `mensajes` (`men_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensaje_receptores_ibfk_2` FOREIGN KEY (`mec_receptor`) REFERENCES `usuarios` (`usu_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `registro_imagenes`
--
ALTER TABLE `registro_imagenes`
  ADD CONSTRAINT `registro_imagenes_ibfk_1` FOREIGN KEY (`rem_id`) REFERENCES `registro_medico` (`rem_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `registro_medico`
--
ALTER TABLE `registro_medico`
  ADD CONSTRAINT `registro_medico_ibfk_1` FOREIGN KEY (`usu_id_medico`) REFERENCES `usuarios` (`usu_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registro_medico_ibfk_2` FOREIGN KEY (`usu_id_paciente`) REFERENCES `usuarios` (`usu_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `registro_movimientos`
--
ALTER TABLE `registro_movimientos`
  ADD CONSTRAINT `registro_movimientos_ibfk_1` FOREIGN KEY (`usu_id`) REFERENCES `usuarios` (`usu_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registro_movimientos_ibfk_2` FOREIGN KEY (`men_id`) REFERENCES `mensajes` (`men_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `registro_movimientos_ibfk_3` FOREIGN KEY (`inf_id`) REFERENCES `informes_usuario` (`inf_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `registro_movimientos_ibfk_4` FOREIGN KEY (`rem_id`) REFERENCES `registro_medico` (`rem_id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`rol_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usuarios_ibfk_2` FOREIGN KEY (`usu_idnave`) REFERENCES `nave` (`nav_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
