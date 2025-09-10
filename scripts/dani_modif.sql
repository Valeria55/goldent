ALTER TABLE `cierres`
	CHANGE COLUMN `sucursal` `sucursal` INT(10) NULL DEFAULT NULL AFTER `cot_dolar`;
ALTER TABLE `ingresos`
	ADD COLUMN `id_usuario_transferencia` INT NULL AFTER `id_gift`;
