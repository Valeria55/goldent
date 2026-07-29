<?php
class entrega
{
    private $pdo;

    public function __CONSTRUCT()
    {
        try {
            $this->pdo = Database::StartUp();
            $this->CrearTablasSiNoExisten();
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    private function CrearTablasSiNoExisten()
    {
        try {
            $sql1 = "CREATE TABLE IF NOT EXISTS `entregas` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `id_presupuesto` INT DEFAULT NULL,
              `id_venta` INT DEFAULT NULL,
              `id_cliente` INT DEFAULT NULL,
              `usuario_asigno_id` INT NOT NULL,
              `responsable_id` INT NOT NULL,
              `usuario_entrega_id` INT DEFAULT NULL,
              `estado` ENUM('Pendiente', 'Entregado', 'Cancelado') DEFAULT 'Pendiente',
              `fecha_asignacion` DATETIME DEFAULT CURRENT_TIMESTAMP,
              `fecha_hora_entrega` DATETIME DEFAULT NULL,
              `importe_percibido` DECIMAL(15,2) DEFAULT 0.00,
              `metodo_pago` VARCHAR(50) DEFAULT NULL,
              `observaciones` TEXT DEFAULT NULL,
              `creado_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
              INDEX `idx_estado` (`estado`),
              INDEX `idx_responsable` (`responsable_id`),
              INDEX `idx_presupuesto` (`id_presupuesto`),
              INDEX `idx_venta` (`id_venta`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->pdo->exec($sql1);

            $sql2 = "CREATE TABLE IF NOT EXISTS `entrega_comprobantes` (
              `id` INT AUTO_INCREMENT PRIMARY KEY,
              `entrega_id` INT NOT NULL,
              `archivo_path` VARCHAR(255) NOT NULL,
              `nombre_original` VARCHAR(255) DEFAULT NULL,
              `tipo_archivo` VARCHAR(50) DEFAULT NULL,
              `creado_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->pdo->exec($sql2);

            try {
                $this->pdo->exec("ALTER TABLE `presupuestos` ADD COLUMN `responsable_entrega_id` INT DEFAULT NULL");
            } catch (Exception $ex) {}

            try {
                $this->pdo->exec("ALTER TABLE `presupuestos` ADD COLUMN `observacion_presupuesto` TEXT DEFAULT NULL");
            } catch (Exception $ex) {}

            try {
                $this->pdo->exec("ALTER TABLE `presupuestos_tmp` ADD COLUMN `responsable_entrega_id` INT DEFAULT NULL");
            } catch (Exception $ex) {}

            try {
                $this->pdo->exec("ALTER TABLE `presupuestos_tmp` ADD COLUMN `observacion_presupuesto` TEXT DEFAULT NULL");
            } catch (Exception $ex) {}

            try {
                $this->pdo->exec("ALTER TABLE `entregas` ADD COLUMN `observacion_presupuesto` TEXT DEFAULT NULL");
            } catch (Exception $ex) {}
        } catch (Exception $e) {
            // Ignorar si ya existen
        }
    }

    public function Registrar($id_presupuesto, $id_cliente, $usuario_asigno_id, $responsable_id, $observacion_presupuesto = null)
    {
        try {
            // Verificar si ya existe una entrega para este presupuesto
            $stmCheck = $this->pdo->prepare("SELECT id FROM entregas WHERE id_presupuesto = ? AND estado != 'Cancelado'");
            $stmCheck->execute(array($id_presupuesto));
            $exist = $stmCheck->fetch(PDO::FETCH_OBJ);

            if ($responsable_id <= 0) {
                // Si el responsable se establece a Sin Asignar (0), cancelar la entrega pendiente previa si existía
                if ($exist) {
                    $stmCancel = $this->pdo->prepare("UPDATE entregas SET estado = 'Cancelado' WHERE id = ? AND estado = 'Pendiente'");
                    $stmCancel->execute(array($exist->id));
                }
                return null;
            }

            if ($exist) {
                // Actualizar responsable u observaciones si cambió
                $stmUpd = $this->pdo->prepare("UPDATE entregas SET responsable_id = ?, usuario_asigno_id = ?, id_cliente = ?, observacion_presupuesto = ? WHERE id = ?");
                $stmUpd->execute(array($responsable_id, $usuario_asigno_id, $id_cliente, $observacion_presupuesto, $exist->id));
                return $exist->id;
            } else {
                $sql = "INSERT INTO entregas (id_presupuesto, id_cliente, usuario_asigno_id, responsable_id, observacion_presupuesto, estado, fecha_asignacion) 
                        VALUES (?, ?, ?, ?, ?, 'Pendiente', NOW())";
                $this->pdo->prepare($sql)->execute(array($id_presupuesto, $id_cliente, $usuario_asigno_id, $responsable_id, $observacion_presupuesto));
                return $this->pdo->lastInsertId();
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function VincularVenta($id_presupuesto, $id_venta)
    {
        try {
            if ($id_presupuesto > 0) {
                $stm = $this->pdo->prepare("UPDATE entregas SET id_venta = ? WHERE id_presupuesto = ?");
                $stm->execute(array($id_venta, $id_presupuesto));
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ConfirmarEntrega($id_entrega, $usuario_entrega_id, $importe_percibido, $metodo_pago, $observaciones)
    {
        try {
            $sql = "UPDATE entregas SET 
                        estado = 'Entregado',
                        usuario_entrega_id = ?,
                        fecha_hora_entrega = NOW(),
                        importe_percibido = ?,
                        metodo_pago = ?,
                        observaciones = ?
                    WHERE id = ?";

            $this->pdo->prepare($sql)->execute(array(
                $usuario_entrega_id,
                $importe_percibido,
                $metodo_pago,
                $observaciones,
                $id_entrega
            ));
            return true;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function GuardarComprobante($entrega_id, $archivo_path, $nombre_original, $tipo_archivo)
    {
        try {
            $sql = "INSERT INTO entrega_comprobantes (entrega_id, archivo_path, nombre_original, tipo_archivo)
                    VALUES (?, ?, ?, ?)";
            $this->pdo->prepare($sql)->execute(array($entrega_id, $archivo_path, $nombre_original, $tipo_archivo));
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ListarPendientes($responsable_id = null)
    {
        try {
            $where = "WHERE e.estado = 'Pendiente'";
            $params = array();

            if ($responsable_id !== null && $responsable_id > 0) {
                $where .= " AND e.responsable_id = ?";
                $params[] = $responsable_id;
            }

            $sql = "SELECT e.*, 
                           c.nombre AS cliente_nombre, c.ruc AS cliente_ruc, c.direccion AS cliente_direccion, c.telefono AS cliente_telefono,
                           u_asigno.user AS usuario_asigno_nombre,
                           u_resp.user AS responsable_nombre,
                           (SELECT SUM((p.cantidad*p.precio_venta)-(((p.cantidad*p.precio_venta)*p.descuento))/100) FROM presupuestos p WHERE p.id_presupuesto = e.id_presupuesto) AS total_presupuesto,
                           (SELECT SUM(v.total) FROM ventas v WHERE v.id_venta = e.id_venta) AS total_venta
                    FROM entregas e
                    LEFT JOIN clientes c ON e.id_cliente = c.id
                    LEFT JOIN usuario u_asigno ON e.usuario_asigno_id = u_asigno.id
                    LEFT JOIN usuario u_resp ON e.responsable_id = u_resp.id
                    $where
                    ORDER BY e.fecha_asignacion DESC";

            $stm = $this->pdo->prepare($sql);
            $stm->execute($params);
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ListarEntregados()
    {
        try {
            $sql = "SELECT e.*, 
                           c.nombre AS cliente_nombre, c.ruc AS cliente_ruc, c.direccion AS cliente_direccion, c.telefono AS cliente_telefono,
                           u_asigno.user AS usuario_asigno_nombre,
                           u_resp.user AS responsable_nombre,
                           u_ent.user AS usuario_entrega_nombre,
                           (SELECT COUNT(*) FROM entrega_comprobantes ec WHERE ec.entrega_id = e.id) AS total_comprobantes
                    FROM entregas e
                    LEFT JOIN clientes c ON e.id_cliente = c.id
                    LEFT JOIN usuario u_asigno ON e.usuario_asigno_id = u_asigno.id
                    LEFT JOIN usuario u_resp ON e.responsable_id = u_resp.id
                    LEFT JOIN usuario u_ent ON e.usuario_entrega_id = u_ent.id
                    WHERE e.estado = 'Entregado'
                    ORDER BY e.fecha_hora_entrega DESC";

            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ListarTodos()
    {
        try {
            $sql = "SELECT e.*, 
                           c.nombre AS cliente_nombre, c.ruc AS cliente_ruc, c.direccion AS cliente_direccion, c.telefono AS cliente_telefono,
                           u_asigno.user AS usuario_asigno_nombre,
                           u_resp.user AS responsable_nombre,
                           u_ent.user AS usuario_entrega_nombre,
                           (SELECT COUNT(*) FROM entrega_comprobantes ec WHERE ec.entrega_id = e.id) AS total_comprobantes
                    FROM entregas e
                    LEFT JOIN clientes c ON e.id_cliente = c.id
                    LEFT JOIN usuario u_asigno ON e.usuario_asigno_id = u_asigno.id
                    LEFT JOIN usuario u_resp ON e.responsable_id = u_resp.id
                    LEFT JOIN usuario u_ent ON e.usuario_entrega_id = u_ent.id
                    ORDER BY e.id DESC";

            $stm = $this->pdo->prepare($sql);
            $stm->execute();
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function Obtener($id)
    {
        try {
            $sql = "SELECT e.*, 
                           c.nombre AS cliente_nombre, c.ruc AS cliente_ruc, c.direccion AS cliente_direccion, c.telefono AS cliente_telefono,
                           u_asigno.user AS usuario_asigno_nombre,
                           u_resp.user AS responsable_nombre,
                           u_ent.user AS usuario_entrega_nombre
                    FROM entregas e
                    LEFT JOIN clientes c ON e.id_cliente = c.id
                    LEFT JOIN usuario u_asigno ON e.usuario_asigno_id = u_asigno.id
                    LEFT JOIN usuario u_resp ON e.responsable_id = u_resp.id
                    LEFT JOIN usuario u_ent ON e.usuario_entrega_id = u_ent.id
                    WHERE e.id = ?";

            $stm = $this->pdo->prepare($sql);
            $stm->execute(array($id));
            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ObtenerComprobantes($entrega_id)
    {
        try {
            $sql = "SELECT * FROM entrega_comprobantes WHERE entrega_id = ? ORDER BY id ASC";
            $stm = $this->pdo->prepare($sql);
            $stm->execute(array($entrega_id));
            return $stm->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    public function ContarPendientes($responsable_id = null)
    {
        try {
            if ($responsable_id !== null && $responsable_id > 0) {
                $sql = "SELECT COUNT(*) AS total FROM entregas WHERE estado = 'Pendiente' AND responsable_id = ?";
                $stm = $this->pdo->prepare($sql);
                $stm->execute(array($responsable_id));
            } else {
                $sql = "SELECT COUNT(*) AS total FROM entregas WHERE estado = 'Pendiente'";
                $stm = $this->pdo->prepare($sql);
                $stm->execute();
            }
            $row = $stm->fetch(PDO::FETCH_OBJ);
            return $row ? $row->total : 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    public function ObtenerPorPresupuesto($id_presupuesto)
    {
        try {
            $sql = "SELECT e.*, 
                           c.nombre AS cliente_nombre, c.ruc AS cliente_ruc, c.direccion AS cliente_direccion, c.telefono AS cliente_telefono,
                           u_asigno.user AS usuario_asigno_nombre,
                           u_resp.user AS responsable_nombre,
                           u_ent.user AS usuario_entrega_nombre
                    FROM entregas e
                    LEFT JOIN clientes c ON e.id_cliente = c.id
                    LEFT JOIN usuario u_asigno ON e.usuario_asigno_id = u_asigno.id
                    LEFT JOIN usuario u_resp ON e.responsable_id = u_resp.id
                    LEFT JOIN usuario u_ent ON e.usuario_entrega_id = u_ent.id
                    WHERE e.id_presupuesto = ? AND e.estado != 'Cancelado' ORDER BY e.id DESC LIMIT 1";

            $stm = $this->pdo->prepare($sql);
            $stm->execute(array($id_presupuesto));
            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            return null;
        }
    }

    public function ObtenerPorVenta($id_venta)
    {
        try {
            $sql = "SELECT e.*, 
                           c.nombre AS cliente_nombre, c.ruc AS cliente_ruc, c.direccion AS cliente_direccion, c.telefono AS cliente_telefono,
                           u_asigno.user AS usuario_asigno_nombre,
                           u_resp.user AS responsable_nombre,
                           u_ent.user AS usuario_entrega_nombre
                    FROM entregas e
                    LEFT JOIN clientes c ON e.id_cliente = c.id
                    LEFT JOIN usuario u_asigno ON e.usuario_asigno_id = u_asigno.id
                    LEFT JOIN usuario u_resp ON e.responsable_id = u_resp.id
                    LEFT JOIN usuario u_ent ON e.usuario_entrega_id = u_ent.id
                    WHERE e.id_venta = ? AND e.estado != 'Cancelado' ORDER BY e.id DESC LIMIT 1";

            $stm = $this->pdo->prepare($sql);
            $stm->execute(array($id_venta));
            return $stm->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            return null;
        }
    }
}
