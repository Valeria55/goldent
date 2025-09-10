<?php
class venta_tmp
{
	private $pdo;

	public $id;
	public $id_venta;
	public $id_vendedor;
	public $id_producto;
	public $precio_venta;
	public $cantidad;
	public $descuento;
	public $fecha_venta;
	public $id_presupuesto;

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
			if (!isset($_SESSION['user_id'])) {
				if (!isset($_SESSION)) session_start();
			}
			$userId = $_SESSION['user_id'];
			$result = array();

			$stm = $this->pdo->prepare("SELECT v.id_presupuesto, v.id, v.id_producto, c.codigo, v.id_vendedor, v.descuento, c.precio_costo, v.precio_venta, c.producto, c.precio_costo, v.cantidad, v.id_venta FROM ventas_tmp v LEFT JOIN productos c ON v.id_producto = c.id WHERE id_vendedor = ? ORDER BY v.id DESC");
			$stm->execute(array($userId));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerMonto()
	{
		try {
			if (!isset($_SESSION['user_id'])) {
				if (!isset($_SESSION)) session_start();
			}
			$user_id = $_SESSION['user_id'];
			$stm = $this->pdo
				->prepare("SELECT *, SUM((precio_venta*cantidad)-(((precio_venta*cantidad)*descuento)/100)) AS monto FROM ventas_tmp  WHERE id_vendedor ='$user_id' GROUP BY id_venta");


			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Obtener()
	{
		try {
			if (!isset($_SESSION['user_id'])) {
				if (!isset($_SESSION)) session_start();
			}
			$user_id = $_SESSION['user_id'];
			$stm = $this->pdo
				->prepare("SELECT *, SUM((precio_venta*cantidad)-((precio_venta*cantidad)*(descuento/100))) as monto FROM ventas_tmp  WHERE id_vendedor = '$user_id' GROUP BY id_venta");


			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Ultimo()
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT MAX(id_venta) as id_venta FROM ventas_tmp");
			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function AgregarDescuento($descuento)
	{
		$user_id = $_SESSION['user_id'];
		try {
			$sql = "UPDATE ventas_tmp SET
						descuento     = ?

					WHERE 
						id_venta = (SELECT MAX(id_venta) FROM ventas_tmp WHERE id_vendedor = $user_id)
						";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$descuento
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerMoneda()
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM monedas WHERE id = 1");


			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Eliminar($id)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM ventas_tmp WHERE id = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Vaciar()
	{
		try {
			if (!isset($_SESSION)) session_start();
			$id_vendedor = $_SESSION['user_id'];
			$stm = $this->pdo
				->prepare("DELETE FROM ventas_tmp WHERE id_vendedor = ? ");
			$stm->execute(array($id_vendedor));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE ventas_tmp SET
						id_venta     = ?,
						id_vendedor     = ?,
						id_producto     = ?,
						precio_venta   = ?,
                        cantidad      = ?, 
						margen_ganancia     = ?,
						fecha_venta      = ?,
						id_presupuesto     = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_venta,
						$data->id_vendedor,
						$data->id_producto,
						$data->precio_venta,
						$data->cantidad,
						$data->margen_ganancia,
						$data->fecha_venta,
						$data->id_presupuesto,
						$data->id
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Moneda($data)
	{
		try {
			$sql = "UPDATE monedas SET
						reales     = ?,
						dolares     = ?,
						monto_inicial = ?
						";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->reales,
						$data->dolares,
						$data->monto_inicial
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Registrar($data)
	{
		try {
			$sql = "INSERT INTO ventas_tmp (id_venta, id_vendedor, id_producto, precio_venta, cantidad, descuento, fecha_venta, id_presupuesto) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_venta,
						$data->id_vendedor,
						$data->id_producto,
						$data->precio_venta,
						$data->cantidad,
						$data->descuento,
						$data->fecha_venta,
						$data->id_presupuesto

					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
