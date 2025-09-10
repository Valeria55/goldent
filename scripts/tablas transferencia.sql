
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Base de datos: `scorecar`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transferencia_productos`
--

CREATE TABLE `transferencia_productos` (
  `id` int(11) NOT NULL,
  `id_transferencia_producto` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `destino_transferencia` varchar(20) DEFAULT NULL,
  `precio_venta` int(11) NOT NULL,
  `cantidad` float NOT NULL,
  `fecha_transferencia_producto` datetime NOT NULL,
  `fecha_confirmacion` datetime DEFAULT NULL,
  `anulado` int(11) NOT NULL DEFAULT 0,
  `descuento` float NOT NULL DEFAULT 0,
  `tipo_transferencia` varchar(20) DEFAULT NULL,
  `estado` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `transferencia_productos`
--
ALTER TABLE `transferencia_productos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `transferencia_productos`
--
ALTER TABLE `transferencia_productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;



--
-- Base de datos: `scorecar`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transferencia_productos_tmp`
--

CREATE TABLE `transferencia_productos_tmp` (
  `id` int(11) NOT NULL,
  `id_transferencia_producto` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `id_producto` varchar(25) NOT NULL,
  `precio_venta` int(11) NOT NULL,
  `cantidad` float NOT NULL,
  `descuento` float NOT NULL,
  `fecha_transferencia_producto` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `transferencia_productos_tmp`
--
ALTER TABLE `transferencia_productos_tmp`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `transferencia_productos_tmp`
--
ALTER TABLE `transferencia_productos_tmp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
