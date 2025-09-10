-- agregar columna apertura_rs y apertura_us en tabla cierres tipo double y no null
ALTER TABLE cierres
ADD COLUMN apertura_rs DOUBLE NOT NULL,
ADD COLUMN apertura_usd DOUBLE NOT NULL;

-- agregar columnas de monto_cierre_rs y monto_cierre_us en tabla cierres tipo double y null
ALTER TABLE cierres
ADD COLUMN monto_cierre_rs DOUBLE NULL,
ADD COLUMN monto_cierre_usd DOUBLE NULL;

-- agregar columna cot_rs y cot_usd en la tabla ventas tipo float y null
ALTER TABLE ventas
ADD COLUMN cot_rs FLOAT NULL,
ADD COLUMN cot_usd FLOAT NULL;

-- agregar columna cot_rs y cot_usd en la tabla COMPRAS tipo float y null
ALTER TABLE compras
ADD COLUMN cot_rs FLOAT NULL,
ADD COLUMN cot_usd FLOAT NULL;

-- agregar columna moneda tipo varchar(3) y null en tabla ventas
ALTER TABLE ventas
ADD COLUMN moneda VARCHAR(3) NULL;

-- agregar columna moneda en pagos_tmp tipo varchar(3) y null
ALTER TABLE pagos_tmp
ADD COLUMN moneda VARCHAR(3) NULL;

-- agregar columna moneda y cambio en ingresos y egresos tipo int
ALTER TABLE ingresos
ADD COLUMN moneda VARCHAR(3) NULL,
ADD COLUMN cambio INT NULL;


ALTER TABLE egresos
ADD COLUMN moneda VARCHAR(3) NULL,
ADD COLUMN cambio INT NULL;

-- agregar columna cambio en pagos_tmp tipo int
ALTER TABLE pagos_tmp
ADD COLUMN cambio INT NULL;

---------- FATLA APLICAR:
-- cambiar el campo monto en tabla ingresos y egresos a float
ALTER TABLE ingresos
MODIFY COLUMN monto FLOAT NOT NULL;

ALTER TABLE egresos
MODIFY COLUMN monto FLOAT NOT NULL;

-- cambiar el campo cambio en tabla ingresos y egresos a float
ALTER TABLE ingresos
MODIFY COLUMN cambio FLOAT NULL;
ALTER TABLE egresos
MODIFY COLUMN cambio FLOAT NULL;

-- -- cambiar el campo monto en tabla pagos_tmp a float
ALTER TABLE pagos_tmp
MODIFY COLUMN monto FLOAT NOT NULL;

-- sql para definir moneda =  GS y cambio 1 en los registros con cambio nulo en ingresos y egresos
UPDATE ingresos
SET moneda = 'GS', cambio = 1
WHERE cambio IS NULL;   

UPDATE egresos
SET moneda = 'GS', cambio = 1
WHERE cambio IS NULL;

-- añadir column a anulado en acreedores con default NULL no booleano
ALTER TABLE acreedores
ADD COLUMN anulado INT DEFAULT NULL;

-- añadr campo id_transferencia a tabla ingresos y egresos por default null
ALTER TABLE ingresos
ADD COLUMN id_transferencia INT DEFAULT NULL;

ALTER TABLE egresos
ADD COLUMN id_transferencia INT DEFAULT NULL;

-- añadir campo aprobado dentro de presupuestos tipo texto default 'si'
ALTER TABLE presupuestos
ADD COLUMN aprobado TEXT DEFAULT 'si';
