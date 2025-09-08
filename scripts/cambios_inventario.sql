DROP TABLE inventario;
DROP TABLE cierre_inventario;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cierre_inventario`
--

CREATE TABLE `cierre_inventario` (
  `id` int(11) NOT NULL,
  `fecha_apertura` datetime DEFAULT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `usuario_inicial` int(11) DEFAULT NULL,
  `usuario_final` int(11) DEFAULT NULL,
  `motivo` text DEFAULT NULL,
  `sobrante_caja` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id` int(11) NOT NULL,
  `id_inventario` int(11) DEFAULT NULL,
  `id_producto` int(11) NOT NULL,
  `id_usuario` varchar(50) DEFAULT NULL,
  `stock_actual` int(11) DEFAULT 0,
  `stock_real` int(11) DEFAULT 0,
  `faltante` int(11) DEFAULT 0,
  `anulado` int(11) NOT NULL DEFAULT 0,
  `fecha` date DEFAULT NULL,
  `fecha_stock_real` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cierre_inventario`
--
ALTER TABLE `cierre_inventario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cierre_inventario`
--
ALTER TABLE `cierre_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;