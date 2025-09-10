--08/08/2023

CREATE TABLE `devoluciones_compras` (
  `id` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `vendedor_salon` int(11) NOT NULL,
  `id_producto` varchar(30) NOT NULL,
  `id_deuda` int(11) NOT NULL,
  `precio_costo` float NOT NULL,
  `precio_compra` int(11) NOT NULL,
  `subtotal` float NOT NULL,
  `descuento` varchar(80) NOT NULL,
  `iva` int(11) NOT NULL,
  `total` float NOT NULL,
  `comprobante` varchar(20) NOT NULL,
  `nro_comprobante` varchar(40) NOT NULL,
  `cantidad` float NOT NULL,
  `margen_ganancia` varchar(45) NOT NULL,
  `fecha_compra` datetime NOT NULL,
  `metodo` varchar(40) NOT NULL,
  `banco` varchar(45) NOT NULL,
  `contado` varchar(30) DEFAULT NULL,
  `anulado` int(11) NOT NULL,
  `motivo` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `deudas` ADD `devolucion` INT NULL AFTER `sucursal`; 

ALTER TABLE `pagos_tmp` ADD `id_deuda` INT NULL AFTER `monto`; 

CREATE TABLE `devoluciones_tmpcompras` (
  `id` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `id_producto` varchar(25) NOT NULL,
  `precio_compra` int(11) NOT NULL,
  `cantidad` float NOT NULL,
  `descuento` varchar(80) NOT NULL,
  `fecha_compra` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Ejecutar para que puedan funcionar las claves primarias
ALTER TABLE `devoluciones_compras` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`id`); 
ALTER TABLE `devoluciones_tmpcompras` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT, add PRIMARY KEY (`id`); 

--17/10/2023
ALTER TABLE `ingresos` ADD `id_compra` INT NULL AFTER `id_usuario_transferencia`; 

--22/11/2023
ALTER TABLE `devoluciones_compras` ADD `sucursal` TEXT NOT NULL AFTER `motivo`; 