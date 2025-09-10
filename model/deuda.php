<?php
class deuda
{
	private $pdo;
	private $pdo_tienda;

	public $id;
	public $id_cliente;
	public $id_venta;
	public $fecha;
	public $vencimiento;
	public $concepto;
	public $monto;
	public $saldo;
	public $sucursal;
	public $devolucion;

	public function __CONSTRUCT()
	{
		try {
			$this->pdo = Database::StartUp();
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Listar()
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, d.id as id, c.id as id_cliente FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE saldo > 0 ORDER BY d.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarSaldados()
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, d.id as id, c.id as id_cliente FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE saldo = 0 ORDER BY d.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarAgrupadoCliente()
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, d.id as id, c.id as id_cliente, SUM(monto) as monto, SUM(saldo) AS saldo FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE saldo > 0 GROUP BY d.id_cliente ORDER BY d.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function listar_cliente_deuda($id)
	{
		try {
			$stm = $this->pdo->prepare("SELECT *, d.id AS id_deuda  
			FROM deudas d
			LEFT JOIN clientes c ON c.id = d.id_cliente
			WHERE id_cliente = ? AND saldo > 0 AND devolucion = 1");
			$stm->execute(array($id));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarDia($fecha)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE Cast(fecha as date) = ?");


			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarMes($fecha)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE MONTH(fecha) = MONTH(?) AND YEAR(fecha) = YEAR(?)");


			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Obtener($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT *, d.id FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE d.id = ?");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Ultimo()
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT MAX(id) as id FROM deudas");
			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function listar_cliente($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM deudas WHERE id_cliente = ? AND saldo > 0");


			$stm->execute(array($id));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Eliminar($id)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM deudas WHERE id = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function AnularVenta($id)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM deudas WHERE id_venta = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function AgrupadoMes($desde, $hasta, $anho)
	{
		try {
			$result = array();
			$mes = date('Y-m') . '-01';
			$fecha = date('Y-m-d');
			if ($anho > 0) {
				$anho = "AND YEAR(i.fecha) = $anho";
				$rango = "";
			} else {
				$anho = "";

				if ($desde != '') {
					if ($hasta != '') {
						$rango = " AND CAST(i.fecha as date) >= '$desde' AND CAST(i.fecha as date) <= '$hasta'";
					} else {
						$rango = " AND CAST(i.fecha as date) >= '$desde' AND CAST(i.fecha as date) <= '$fecha'";
					}
				} else {
					if ($hasta != '') {
						$rango = " AND CAST(i.fecha as date) >= '$mes' AND CAST(i.fecha as date) <= '$hasta'";
					} else {
						$rango = " AND CAST(i.fecha as date) >= '$mes' AND CAST(i.fecha as date) <= '$fecha'";
					}
				}
			}

			$sql = "SELECT i.concepto, i.fecha, SUM(i.monto) as monto, SUM(i.saldo) as saldo, c.nombre AS nombre 
			FROM deudas i
			LEFT JOIN clientes c ON c.id=i.id_cliente
			WHERE saldo > 0 $rango $anho 
			GROUP BY i.id_cliente ORDER BY i.id DESC";

			$stm = $this->pdo->prepare($sql);
			$stm->execute();
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function SumarSaldo($data)
	{
		try {
			$sql = "UPDATE deudas SET saldo = saldo + ? WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array($data->monto, $data->id)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE deudas SET 
						id_cliente    = ?,
						id_venta      = ?,
						fecha      	  = ?,
						vencimiento	  = ?,
						concepto      = ?, 
						monto         = ?,
						saldo         = ?,
						sucursal      = ?,
						devolucion      = ?
                        
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_cliente,
						$data->id_venta,
						$data->fecha,
						$data->vencimiento,
						$data->concepto,
						$data->monto,
						$data->saldo,
						$data->sucursal,
						$data->devolucion,
						$data->id
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function EditarMonto($id_venta, $monto)
	{
		try {
			$sql = "UPDATE deudas SET 
						monto    = ?
				    WHERE id_venta = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$monto,
						$id_venta
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Restar($data)
	{
		try {
			$sql = "UPDATE deudas SET 
					
					saldo = saldo - ?
                        
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->monto,
						$data->id
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Guardar($data)
	{
		try {
			$sql = "INSERT INTO deudas (id_cliente, id_venta, fecha, vencimiento, concepto, monto, saldo, sucursal, devolucion) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_cliente,
						$data->id_venta,
						$data->fecha,
						$data->vencimiento,
						$data->concepto,
						$data->monto,
						$data->saldo,
						$data->sucursal,
						$data->devolucion
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Registrar($data)
	{
		try {
			$sql = "INSERT INTO deudas (id_cliente, id_venta, fecha, vencimiento, concepto, monto, saldo, sucursal, devolucion) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_cliente,
						$data->id_venta,
						$data->fecha,
						$data->vencimiento,
						$data->concepto,
						$data->monto,
						$data->saldo,
						$data->sucursal,
						$data->devolucion
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function RegistrarTaller($data)
	{
		try {
			// Asegúrate de que los datos de entrada sean válidos
			// (Aquí podrías añadir validaciones específicas para cada campo)
			// ...	
			$this->pdo_tienda = Database::StartUp_taller();

			$sql = "INSERT INTO deudas (id_cliente, id_venta, fecha, vencimiento, concepto, monto, saldo, sucursal, devolucion) 
					VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$stmt = $this->pdo_tienda->prepare($sql);

			// Ejecución de la consulta preparada con manejo de errores
			if ($stmt->execute([
				$data->id_cliente,
				$data->id_venta,
				$data->fecha,
				$data->vencimiento,
				$data->concepto,
				$data->monto,
				$data->saldo,
				$data->sucursal,
				$data->devolucion
			])) {
				// Opcional: Devuelve algún indicador de éxito o el ID de la fila insertada
				return ['success' => true, 'id' => $this->pdo_tienda->lastInsertId()];
			} else {
				// Manejar el caso en que la ejecución falla
				return ['success' => false, 'error' => 'Error al insertar datos.'];
			}
		} catch (PDOException $e) {
			// Manejo de errores específicos de la base de datos
			return ['success' => false, 'error' => $e->getMessage()];
		} finally {
			// Asegurar que la conexión se cierre en cualquier caso
			$this->pdo_tienda = null;
		}
	}

	/**
	 * Obtiene resumen de cuentas por cobrar para un período específico
	 */
	public function ResumenCuentasPorCobrar($desde, $hasta)
	{
		try {
			// SALDO ANTERIOR: TODO el monto de las deudas menos ingresos de cobro de deudas anteriores
			$stm = $this->pdo->prepare("
				SELECT COALESCE(SUM(monto), 0) as total_montos_deudas
				FROM deudas
			");
			$stm->execute();
			$total_montos_deudas = $stm->fetch(PDO::FETCH_OBJ)->total_montos_deudas;

			// Obtener cobros de deudas anteriores a la fecha especificada (ingresos con cobro de deudas)
			$stm = $this->pdo->prepare("
				SELECT COALESCE(SUM(monto * COALESCE(cambio, 1)), 0) as cobros_deudas_anteriores
				FROM ingresos 
				WHERE fecha < ? 
				AND (categoria = 'Cobro de deuda' OR categoria LIKE '%cobro%' OR categoria LIKE '%deuda%')
				AND anulado IS NULL
			");
			$stm->execute(array($desde));
			$cobros_deudas_anteriores = $stm->fetch(PDO::FETCH_OBJ)->cobros_deudas_anteriores;

			$saldo_anterior = $total_montos_deudas - $cobros_deudas_anteriores;

			// VENTAS A CRÉDITO: ventas con estado 'APROBADO' no anuladas desde la fecha especificada
			$stm = $this->pdo->prepare("
				SELECT COALESCE(SUM(v.total), 0) as ventas_credito
				FROM ventas v
				WHERE v.fecha_venta >= ? AND v.fecha_venta <= ?
				AND v.contado = 'Credito'
				AND v.estado = 'APROBADO'
				AND v.anulado = 0
			");
			$stm->execute(array($desde, $hasta));
			$ventas_credito = $stm->fetch(PDO::FETCH_OBJ)->ventas_credito;

			// COBROS RECIBIDOS: cobros hechos a esas ventas a crédito del período
			$stm = $this->pdo->prepare("
				SELECT COALESCE(SUM(i.monto * COALESCE(i.cambio, 1)), 0) as cobros_recibidos
				FROM ingresos i
				INNER JOIN ventas v ON i.id_venta = v.id_venta
				WHERE v.fecha_venta >= ? AND v.fecha_venta <= ?
				AND v.contado = 'Credito'
				AND v.estado = 'APROBADO'
				AND v.anulado IS NULL
				AND i.anulado IS NULL
				AND i.categoria != 'Transferencia'
			");
			$stm->execute(array($desde, $hasta));
			$cobros_recibidos = $stm->fetch(PDO::FETCH_OBJ)->cobros_recibidos;

			// SALDO FINAL: saldo anterior + ventas a crédito - cobros recibidos
			$saldo_final = $saldo_anterior + $ventas_credito - $cobros_recibidos;

			return (object) [
				'saldo_anterior' => $saldo_anterior,
				'ventas_credito' => $ventas_credito,
				'cobros_recibidos' => $cobros_recibidos,
				'saldo_final' => $saldo_final
			];
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
