<?php
class compra_tmp
{
	private $pdo;

	public $id;
	public $id_compra;
	public $id_vendedor;
	public $id_producto;
	public $precio_compra;
	public $precio_min;
	public $precio_may;
	public $cantidad;
	public $fecha_compra;

	public function __CONSTRUCT()
	{
		try {
			$this->pdo = Database::StartUp();
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Listar($id_compra = null)
	{
		try {
			if (session_status() != PHP_SESSION_ACTIVE) {
				if (!isset($_SESSION)) session_start();
			}
			$userId = $_SESSION['user_id'];
			$result = array();

			if ($id_compra !== null) {
				// Si se especifica id_compra, filtrar por ese valor
				$stm = $this->pdo->prepare("SELECT v.id, v.id_producto, v.id_vendedor, c.precio_costo, v.precio_compra, v.precio_min, v.precio_may, c.producto, c.precio_costo, v.cantidad, v.id_compra FROM compras_tmp v LEFT JOIN productos c ON v.id_producto = c.id WHERE v.id_vendedor = ? AND v.id_compra = ? ORDER BY v.id DESC");
				$stm->execute(array($userId, $id_compra));
			} else {
				// Comportamiento original sin filtro por id_compra
				$stm = $this->pdo->prepare("SELECT (SELECT MAX(id_compra+1) FROM compras) AS id_compra, v.id, v.id_producto, v.id_vendedor, c.precio_costo, v.precio_compra, v.precio_min, v.precio_may, c.producto, c.precio_costo, v.cantidad FROM compras_tmp v LEFT JOIN productos c ON v.id_producto = c.id WHERE id_vendedor = ? ORDER BY v.id DESC");
				$stm->execute(array($userId));
			}

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerSinID()
	{
		try {
			if (!isset($_SESSION['user_id'])) {
				if (!isset($_SESSION)) session_start();
			}
			$user_id = $_SESSION['user_id'];
			$stm = $this->pdo->prepare("SELECT *, SUM((precio_compra*cantidad)) as monto FROM compras_tmp  WHERE id_vendedor = '$user_id' GROUP BY id_compra");

			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Obtener($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM compras_tmp WHERE id = ?");


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

	public function ObtenerMonto($id_compra = null)
	{
		try {
			if (!isset($_SESSION['user_id'])) {
				if (!isset($_SESSION)) session_start();
			}
			$user_id = $_SESSION['user_id'];
			
			if ($id_compra) {
				// Para edición: obtener monto de compra específica
				$stm = $this->pdo->prepare("SELECT *, SUM((precio_compra*cantidad)) as monto FROM compras_tmp WHERE id_vendedor = ? AND id_compra = ? GROUP BY id_compra");
				$stm->execute(array($user_id, $id_compra));
			} else {
				// Para compra nueva: obtener monto general
				$stm = $this->pdo->prepare("SELECT *, SUM((precio_compra*cantidad)) as monto FROM compras_tmp WHERE id_vendedor = ? GROUP BY id_compra");
				$stm->execute(array($user_id));
			}
			
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	
	public function Eliminar($id)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM compras_tmp WHERE id = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Vaciar()
	{
		try {
			if (!isset($_SESSION)) session_start();
			$stm = $this->pdo
				->prepare("DELETE FROM compras_tmp WHERE id_vendedor = ?");
			$stm->execute(array($_SESSION["user_id"]));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function VaciarPorCompra($id_compra)
	{
		try {
			if (!isset($_SESSION)) session_start();
			$userId = $_SESSION['user_id'];
			
			$stm = $this->pdo->prepare("DELETE FROM compras_tmp WHERE id_vendedor = ? AND id_compra = ?");
			$stm->execute(array($userId, $id_compra));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE compras_tmp SET
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
			$sql = "INSERT INTO compras_tmp (id_compra, id_vendedor, id_producto, precio_compra, precio_min, precio_may, cantidad, fecha_compra) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_compra,
						$data->id_vendedor,
						$data->id_producto,
						$data->precio_compra,
						$data->precio_min,
						$data->precio_may,
						$data->cantidad,
						$data->fecha_compra

					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
