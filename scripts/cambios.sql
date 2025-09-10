"ALTER TABLE `productos` ADD UNIQUE(`codigo`);


-- tabla productos --

ALTER TABLE `productos` ADD `preciob` INT NULL AFTER `precio_mayorista`;



-- transferencia_tmp --

CREATE TABLE `scorecar`.`transferencias_tmp` (`id` INT(11) NOT NULL AUTO_INCREMENT , `id_venta` INT(11) NULL , `id_vendedor` INT(11) NULL , `id_producto` INT(11) NULL , `precio_venta` INT NULL , `cantidad` INT NULL , `descuento` INT NULL , `fecha_venta` INT NULL , `id_presupuesto` INT(11) NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;