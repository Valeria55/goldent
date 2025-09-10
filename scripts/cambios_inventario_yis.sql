



-- BORRAR LAS TABLAS INVENTARIO Y CIERRE INVENTARIO Y DESPUÉS EJECUTAR ESTE SCRIPT!!


--
-- Estructura de tabla para la tabla `inventario`
--
CREATE TABLE `inventario` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_usuario` varchar(50) DEFAULT NULL,
  `stock_actual` int(11) DEFAULT 0,
  `stock_real` int(11) DEFAULT 0,
  `faltante` int(11) DEFAULT 0,
  `anulado` int(11) NOT NULL DEFAULT 0,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`);

-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
--
-- Estructura de tabla para la tabla `cierre_inventario`
--

CREATE TABLE `cierre_inventario` (
  `id` int(11) NOT NULL,
  `fecha_apertura` datetime DEFAULT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `usuario_inicial` int(11) DEFAULT NULL,
  `usuario_final` int(11) DEFAULT NULL,
  `motivo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cierre_inventario`
--
ALTER TABLE `cierre_inventario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cierre_inventario`
--
ALTER TABLE `cierre_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;


ALTER TABLE `inventario` ADD `id_inventario` INT(11) NULL AFTER `id`;

ALTER TABLE `inventario` ADD `fecha_stock_real` DATETIME NULL AFTER `fecha`;

ALTER TABLE `cierre_inventario` ADD `sobrante_caja` FLOAT NULL AFTER `motivo`;

ALTER TABLE `inventario` ADD `stock_tabla_productos` INT NULL AFTER `stock_actual`;
