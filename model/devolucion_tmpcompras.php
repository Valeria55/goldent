<?php
class devolucion_tmpcompras
{
	private $pdo;

	public $id;
	public $id_compra;
	public $id_vendedor;
	public $id_producto;
	public $precio_compra;
	public $cantidad;
	public $descuento;
	public $fecha_compra;

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

			$stm = $this->pdo->prepare(
				"SELECT 
					v.id, v.id_producto, v.id_vendedor, 
					v.descuento, c.precio_costo, 
					v.precio_compra, c.producto, 
					c.precio_costo, v.cantidad, v.id_compra
				FROM devoluciones_tmpcompras v 
					LEFT JOIN productos c ON v.id_producto = c.id 
				WHERE id_vendedor = ? 
				ORDER BY v.id DESC");
			$stm->execute(array($userId));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Obtener($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM devoluciones_tmpcompras WHERE id = ?");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
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
				->prepare("DELETE FROM devoluciones_tmpcompras WHERE id = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Vaciar()
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM devoluciones_tmpcompras ");
			$stm->execute();
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE devoluciones_tmpcompras SET
						id_compra     = ?,
						id_vendedor     = ?,
						id_producto     = ?,
						precio_compra   = ?,
                        cantidad      = ?, 
						margen_ganancia     = ?,
						fecha_compra      = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_compra,
						$data->id_vendedor,
						$data->id_producto,
						$data->precio_compra,
						$data->cantidad,
						$data->margen_ganancia,
						$data->fecha_compra,
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
			$sql = "INSERT INTO devoluciones_tmpcompras (id_compra, id_vendedor, id_producto, precio_compra, cantidad, descuento, fecha_compra) 
		        VALUES (?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_compra,
						$data->id_vendedor,
						$data->id_producto,
						$data->precio_compra,
						$data->cantidad,
						$data->descuento,
						$data->fecha_compra

					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
