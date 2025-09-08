
ALTER TABLE `productos` CHANGE `precio_costo` `precio_costo` INT NOT NULL;
ALTER TABLE `productos` CHANGE `precio_minorista` `precio_minorista` INT NOT NULL DEFAULT '0';
ALTER TABLE `productos` CHANGE `precio_mayorista` `precio_mayorista` INT NULL DEFAULT '0';
ALTER TABLE `productos` CHANGE `ultimo_precio` `ultimo_precio` INT NULL DEFAULT NULL;
ALTER TABLE `productos` CHANGE `precio_promo` `precio_promo` INT NULL DEFAULT NULL;



ALTER TABLE `productos` DROP `precio_brasil`;
ALTER TABLE `productos` CHANGE `precio_turista` `precio_intermedio` INT NULL DEFAULT NULL;

/* ================================ 
    VACIAR precio_promedio
================================ */
UPDATE productos
SET precio_intermedio = NULL;



ALTER TABLE `compras_tmp` DROP `precio_brasil`;
ALTER TABLE `compras_tmp` CHANGE `precio_turista` `precio_intermedio` INT NULL DEFAULT NULL;
ALTER TABLE `compras_tmp` CHANGE `precio_compra` `precio_compra` INT NOT NULL;ALTER TABLE `compras_tmp` CHANGE `precio_min` `precio_min` INT NOT NULL;
ALTER TABLE `compras_tmp` CHANGE `precio_may` `precio_may` INT NOT NULL;

ALTER TABLE `compras` CHANGE `precio_compra` `precio_compra` INT NOT NULL;
ALTER TABLE `compras` CHANGE `precio_min` `precio_min` INT NOT NULL;
ALTER TABLE `compras` CHANGE `precio_may` `precio_may` INT NOT NULL;
ALTER TABLE `compras` CHANGE `subtotal` `subtotal` INT NOT NULL;
ALTER TABLE `compras` CHANGE `total` `total` INT NOT NULL;

ALTER TABLE `presupuesto_compras_tmp` CHANGE `precio_compra` `precio_compra` INT NOT NULL;
ALTER TABLE `presupuesto_compras_tmp` CHANGE `precio_min` `precio_min` INT NOT NULL;
ALTER TABLE `presupuesto_compras_tmp` CHANGE `precio_may` `precio_may` INT NOT NULL;
ALTER TABLE `presupuesto_compras_tmp` DROP `precio_brasil`;
ALTER TABLE `presupuesto_compras_tmp` CHANGE `precio_turista` `precio_intermedio` INT NULL DEFAULT NULL;

ALTER TABLE `presupuesto_compras` CHANGE `precio_compra` `precio_compra` INT NOT NULL;
ALTER TABLE `presupuesto_compras` CHANGE `precio_min` `precio_min` INT NOT NULL;
ALTER TABLE `presupuesto_compras` CHANGE `precio_may` `precio_may` INT NOT NULL;
ALTER TABLE `presupuesto_compras` DROP `precio_brasil`;
ALTER TABLE `presupuesto_compras` CHANGE `precio_turista` `precio_intermedio` INT NULL DEFAULT NULL;
ALTER TABLE `presupuesto_compras` CHANGE `subtotal` `subtotal` INT NOT NULL;
ALTER TABLE `presupuesto_compras` CHANGE `total` `total` INT NOT NULL;

/* ================================ 
    Corrección categorias
================================ */
UPDATE `egresos` SET `categoria` = 'Gastos por compra' WHERE `egresos`.`categoria` = 'Gatos por compra' LIMIT 1