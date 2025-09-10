<?php
class presupuesto_tmp
{
	private $pdo;

	public $id;
	public $id_presupuesto;
	public $id_vendedor;
	public $id_producto;
	public $precio_venta;
	public $cantidad;
	public $descuento;
	public $fecha_presupuesto;

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

			$stm = $this->pdo->prepare("SELECT v.id, v.id_producto,v.descuento, p.codigo, v.id_vendedor, v.precio_venta, p.producto, p.precio_costo, v.cantidad, v.id_presupuesto
			FROM presupuestos_tmp v 
			LEFT JOIN productos p ON v.id_producto = p.id
			WHERE id_vendedor = ? ORDER BY v.id DESC");
			$stm->execute(array($userId));

			return $stm->fetchAll(PDO::FETCH_OBJ);
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
				->prepare("SELECT *, SUM((precio_venta*cantidad)-descuento) as monto FROM presupuestos_tmp  WHERE id_vendedor = '$user_id' GROUP BY id_presupuesto");


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
				->prepare("SELECT MAX(id_presupuesto) as id_presupuesto FROM presupuestos_tmp");
			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ObtenerPorProductoUsuario($data)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM presupuestos_tmp WHERE id_producto = ? AND id_vendedor = ?");
			          

			$stm->execute(array($data->id_producto, $data->id_vendedor));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
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
				->prepare("DELETE FROM presupuestos_tmp WHERE id = ?");

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
				->prepare("DELETE FROM presupuestos_tmp WHERE id_vendedor = ? ");
			$stm->execute(array($id_vendedor));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE presupuestos_tmp SET
						id_presupuesto     = ?,
						id_vendedor     = ?,
						id_producto     = ?,
						precio_venta   = ?,
                        cantidad      = ?, 
						margen_ganancia     = ?,
						fecha_presupuesto      = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_presupuesto,
						$data->id_vendedor,
						$data->id_producto,
						$data->precio_venta,
						$data->cantidad,
						$data->margen_ganancia,
						$data->fecha_presupuesto,
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
			$sql = "INSERT INTO presupuestos_tmp (id_presupuesto, id_vendedor, id_producto, precio_venta, cantidad, descuento, fecha_presupuesto) 
		        VALUES (?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_presupuesto,
						$data->id_vendedor,
						$data->id_producto,
						$data->precio_venta,
						$data->cantidad,
						$data->descuento,
						$data->fecha_presupuesto

					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
