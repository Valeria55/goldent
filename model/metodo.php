<?php
class metodo
{
	private $pdo;

	public $id;
	public $metodo;

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

			$stm = $this->pdo->prepare("SELECT *, (SELECT SUM(i.monto) FROM ingresos i WHERE i.forma_pago = m.metodo AND i.anulado IS NULL) AS ingresos, (SELECT SUM(e.monto) FROM egresos e WHERE e.forma_pago = m.metodo AND CAST(fecha AS date)>'2022-09-23' AND categoria <> 'compra' AND e.anulado IS NULL) AS egresos FROM metodos m WHERE m.anulado = 0");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarMovimientosFecha($desde, $hasta, $anho, $metodo)
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

			// die("SELECT i.fecha, i.categoria, i.concepto, i.comprobante, (i.monto * 1) as monto, i.forma_pago, i.anulado, (SELECT v.descuento FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as descuento FROM ingresos i WHERE forma_pago = ? $rango $anho AND anulado IS NULL 
			// UNION ALL 
			// SELECT i.fecha, i.categoria, i.concepto, i.comprobante, (i.monto * -1) as monto, i.forma_pago, i.anulado, (SELECT v.descuento FROM compras v WHERE v.id_compra = i.id_compra LIMIT 1) as descuento FROM egresos i WHERE i.forma_pago = ? $rango $anho AND anulado IS NULL");

			$query = "SELECT i.fecha, i.categoria, i.concepto, i.comprobante, (i.monto * 1) as monto, i.forma_pago, i.anulado, (SELECT v.descuento FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as descuento FROM ingresos i WHERE forma_pago = ? $rango $anho AND anulado IS NULL 
			UNION ALL 
			SELECT i.fecha, i.categoria, i.concepto, i.comprobante, (i.monto * -1) as monto, i.forma_pago, i.anulado, (SELECT v.descuento FROM compras v WHERE v.id_compra = i.id_compra LIMIT 1) as descuento FROM egresos i WHERE i.forma_pago = ? $rango $anho AND anulado IS NULL";

			$stm = $this->pdo->prepare($query);
			$stm->execute(array($metodo, $metodo));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarMovimientos($metodo)
	{
		try {
			$result = array();
			$query = "SELECT i.fecha, i.categoria, i.concepto, i.comprobante, (i.monto * 1) as monto, i.forma_pago, i.anulado, (SELECT v.descuento FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as descuento FROM ingresos i WHERE forma_pago = ? AND anulado IS NULL
				UNION ALL 
				SELECT e.fecha, e.categoria, e.concepto, e.comprobante, (e.monto * -1) as monto, e.forma_pago, e.anulado, (SELECT v.descuento FROM compras v WHERE v.id_compra = e.id_compra LIMIT 1) as descuento FROM egresos e WHERE e.forma_pago = ? AND anulado IS NULL ";
			$stm = $this->pdo->prepare($query);
			$stm->execute(array($metodo, $metodo));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Obtener($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT *, SUM(SELECT monto FROM ingresos WHERE forma_pago = metodo) AS total FROM metodos WHERE id = ?");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Eliminar($id)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM pagos_tmp WHERE id = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Anular($id)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE metodos SET anulado = 1 WHERE id = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Vaciar()
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM pagos_tmp ");

			$stm->execute(array());
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE metodos SET 

						metodo   = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->metodo,
						$data->id
					)
				);
			return "Modificado";
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Registrar(metodo $data)
	{
		try {
			$sql = "INSERT INTO metodos (metodo) 
		        VALUES (?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->metodo
					)
				);
			return "Agregado";
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarTodos()
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM metodos m WHERE m.anulado = 0 ");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
