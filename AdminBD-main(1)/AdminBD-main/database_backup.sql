-- ==========================================================
-- COPIA DE SEGURIDAD AUTOMÁTICA - QUEHAYPAHACER
-- Generada el: 2026-10-08 02:23:28
-- ==========================================================

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------------------------------------
-- Estructura y datos para la tabla `auditoria`
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `auditoria`;
CREATE TABLE `auditoria` (
  `id_auditoria` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario_admin` int(11) NOT NULL,
  `accion` varchar(255) NOT NULL,
  `tabla_afectada` varchar(50) NOT NULL,
  `registro_id` int(11) NOT NULL,
  `fecha_accion` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_auditoria`),
  KEY `id_usuario_admin` (`id_usuario_admin`),
  CONSTRAINT `auditoria_ibfk_1` FOREIGN KEY (`id_usuario_admin`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `auditoria` (`id_auditoria`, `id_usuario_admin`, `accion`, `tabla_afectada`, `registro_id`, `fecha_accion`) VALUES
  ('1', '1', 'Cambio de estado a Aprobado', 'Comercios', '1', '2026-09-23 17:01:57'),
  ('2', '1', 'Cambio de estado a Aprobado', 'Comercios', '2', '2026-10-07 19:14:21');

-- ----------------------------------------------------------
-- Estructura y datos para la tabla `categorias`
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('Comercio','Turismo') NOT NULL,
  `descripcion` text DEFAULT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categorias` (`id_categoria`, `nombre`, `tipo`, `descripcion`) VALUES
  ('1', 'Restaurantes y Comida', 'Comercio', 'Lugares para comer o beber.'),
  ('2', 'Hoteles y Hospedaje', 'Comercio', 'Sitios para descansar y dormir.'),
  ('3', 'Tiendas y Supermercados', 'Comercio', 'Compra de víveres y productos.'),
  ('4', 'Bares y Discotecas', 'Comercio', 'Entretenimiento nocturno.'),
  ('5', 'Ecoturismo y Aventura', 'Turismo', 'Actividades al aire libre.'),
  ('6', 'Museos y Cultura', 'Turismo', 'Sitios de interés cultural e histórico.');

-- ----------------------------------------------------------
-- Estructura y datos para la tabla `comercios`
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `comercios`;
CREATE TABLE `comercios` (
  `id_comercio` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `id_usuario_propietario` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `estado` enum('Pendiente','Aprobado','Rechazado') DEFAULT 'Pendiente',
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_comercio`),
  KEY `id_usuario_propietario` (`id_usuario_propietario`),
  KEY `id_categoria` (`id_categoria`),
  CONSTRAINT `comercios_ibfk_1` FOREIGN KEY (`id_usuario_propietario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `comercios_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `comercios` (`id_comercio`, `nombre`, `descripcion`, `direccion`, `telefono`, `id_usuario_propietario`, `id_categoria`, `estado`, `fecha_creacion`) VALUES
  ('1', 'Bar', 'Licores', 'Cra111#111a11', '3123456787', '1', '4', 'Aprobado', '2026-09-23 17:00:02'),
  ('2', 'Discoteca', 'Música y fiestas', 'Tv-12#9.3', '3123456787', '3', '4', 'Aprobado', '2026-10-07 19:14:02');

-- ----------------------------------------------------------
-- Estructura y datos para la tabla `roles`
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id_rol`, `nombre`) VALUES
  ('2', 'Dueño de Comercio'),
  ('1', 'Superadministrador'),
  ('3', 'Usuario Final');

-- ----------------------------------------------------------
-- Estructura y datos para la tabla `turismo`
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `turismo`;
CREATE TABLE `turismo` (
  `id_actividad` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `ubicacion` varchar(255) NOT NULL,
  `precio_estimado` decimal(10,2) DEFAULT NULL,
  `id_usuario_proveedor` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `estado` enum('Pendiente','Aprobado','Rechazado') DEFAULT 'Pendiente',
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_actividad`),
  KEY `id_usuario_proveedor` (`id_usuario_proveedor`),
  KEY `id_categoria` (`id_categoria`),
  CONSTRAINT `turismo_ibfk_1` FOREIGN KEY (`id_usuario_proveedor`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `turismo_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- Estructura y datos para la tabla `usuarios`
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `email`, `password_hash`, `id_rol`, `fecha_registro`, `activo`) VALUES
  ('1', 'Superadministrador', 'admin', '$2y$10$dKJTPNrI6iGx1GzPPAlvHO4Mdhd3EOWzLhUs1F7S/U9fyiFFry8kO', '1', '2026-09-23 16:53:01', '1'),
  ('2', 'Sergio', 'sergio.rodriguezr@cun.edu.co', '$2y$10$uSDtKEXX7uv40NiwZVZZlujUavJR2zM5a4AWV3HWTwjOHbWPt0CLi', '3', '2026-10-07 19:08:16', '1'),
  ('3', 'Parra', 'sergio.parrai@cun.edu.co', '$2y$10$j/IvxL7/VpWGCWpz42wKNeli.T78UySaWOdtUKgzPvdcpySulUqM2', '2', '2026-10-07 19:08:58', '1'),
  ('5', 'Santiago', 'david.forerog@cun.edu.co', '$2y$10$nnmwnal.N3hnlYJ0UNmH2OR0sZzcwak2uV49OutcN3hz982qRH.fi', '1', '2026-10-07 19:11:41', '1'),
  ('6', 'David Robayo', 'juegossergio24@gmail.com', '$2y$10$QrPd6A0000nk7b9WkSwSDeaC7QQ8lwRkw.XSRpg5ZgLJsHd0x2ZuW', '2', '2026-10-07 19:20:48', '1');

SET FOREIGN_KEY_CHECKS=1;
