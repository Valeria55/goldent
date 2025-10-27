<?php
class presupuesto
{
	private $pdo;

	public $id;
	public $id_presupuesto;
	public $id_cliente;
	public $id_vendedor;
	public $id_producto;
	public $precio_venta;
	public $cantidad;
	public $fecha_presupuesto;
	public $anulado;
	public $descuento;
	public $estado;
	public $aprobado;
	public $paciente;

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
			if (!isset($_SESSION)) session_start();
			$userId = $_SESSION['user_id'];
			if (($_SESSION['nivel'] <> 3)) {
				$vendedor = "";
			} else {
				$vendedor = "AND v.id_vendedor = '$userId'";
			}

			$result = array();

			$stm = $this->pdo->prepare("SELECT *, SUM((v.cantidad*v.precio_venta)-(((v.cantidad*v.precio_venta)*v.descuento))/100) AS total
			FROM presupuestos v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE 1=1 $vendedor GROUP BY v.id_presupuesto ORDER BY v.fecha_presupuesto DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
		public function ListarFiltros($desde, $hasta)
	{
		try {
			if (!isset($_SESSION)) session_start();
			$userId = $_SESSION['user_id'];
			if (($_SESSION['nivel'] <> 3)) {
				$vendedor = "";
			} else {
				$vendedor = "AND v.id_vendedor = '$userId'";
			}
			$mes=date('m');
			$rango=$desde==!''?"AND CAST(fecha_presupuesto AS date)>='$desde' AND CAST(fecha_presupuesto AS date)<='$hasta' ":"AND MONTH(fecha_presupuesto)='$mes'";

			$result = array();

			$stm = $this->pdo->prepare("SELECT *, SUM((v.cantidad*v.precio_venta)-(((v.cantidad*v.precio_venta)*v.descuento))/100) AS total
			FROM presupuestos v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE 1=1 $vendedor $rango GROUP BY v.id_presupuesto ORDER BY v.id DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarAnulado()
	{
		try {
			if (!isset($_SESSION)) session_start();
			$userId = $_SESSION['user_id'];
			if (($_SESSION['nivel'] <> 3)) {
				$vendedor = "";
			} else {
				$vendedor = "AND v.id_vendedor = '$userId'";
			}

			$result = array();

			$stm = $this->pdo->prepare("SELECT *, SUM((v.cantidad*v.precio_venta)-(((v.cantidad*v.precio_venta)*v.descuento))/100) AS total
			FROM presupuestos v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE v.anulado=1 $vendedor GROUP BY v.id_presupuesto ORDER BY v.id DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarPendientes()
	{
		try {
			if (!isset($_SESSION)) session_start();
			$userId = $_SESSION['user_id'];
			if (($_SESSION['nivel'] <> 3)) {
				$vendedor = "";
			} else {
				$vendedor = "AND v.id_vendedor = '$userId'";
			}

			$result = array();

			$stm = $this->pdo->prepare("SELECT *, SUM((v.cantidad*v.precio_venta)-(((v.cantidad*v.precio_venta)*v.descuento))/100) AS total
			FROM presupuestos v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE v.aprobado='no' AND v.anulado!=1 $vendedor GROUP BY v.id_presupuesto ORDER BY v.id DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	

	public function ListarFiltrosAnulado($desde, $hasta)
	{
		try {
			if (!isset($_SESSION)) session_start();
			$userId = $_SESSION['user_id'];
			if (($_SESSION['nivel'] <> 3)) {
				$vendedor = "";
			} else {
				$vendedor = "AND v.id_vendedor = '$userId'";
			}

			$result = array();

			$stm = $this->pdo->prepare("SELECT *, SUM((v.cantidad*v.precio_venta)-(((v.cantidad*v.precio_venta)*v.descuento))/100) AS total
			FROM presupuestos v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE 1=1 $vendedor GROUP BY v.id_presupuesto ORDER BY v.id DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarOrden($id_presupuesto)
	{
		try {

			$result = array();

			$stm = $this->pdo->prepare("SELECT v.*, c.nombre, c.ruc AS ruc, p.producto
			FROM presupuestos v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE id_presupuesto = ? ORDER BY v.id DESC");
			$stm->execute(array($id_presupuesto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarDetalle($id_presupuesto)
	{
		try {

			$result = array();

			$stm = $this->pdo->prepare("SELECT *
			FROM presupuestos v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE id_presupuesto = ? ORDER BY v.id DESC");
			$stm->execute(array($id_presupuesto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarPresupuesto($id_presupuesto)
	{
		try {

			$result = array();

			$stm = $this->pdo->prepare("SELECT *, SUM((v.cantidad*v.precio_venta)-(((v.cantidad*v.precio_venta)*v.descuento))/100) AS total
			FROM presupuestos v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE id_presupuesto = ? ORDER BY v.id DESC");
			$stm->execute(array($id_presupuesto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ObtenerId_presupuesto($id)
	{
		try {
			$stm = $this->pdo->prepare("SELECT * FROM presupuestos WHERE id_presupuesto = ?");


			$stm->execute(array($id));
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
				->prepare("SELECT * FROM presupuestos  WHERE id_vendedor = '$user_id' GROUP BY id_presupuesto");


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
				->prepare("SELECT MAX(id_presupuesto) as id_presupuesto FROM presupuestos");
			$stm->execute();
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
				->prepare("DELETE FROM presupuestos WHERE id = ?");

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
				->prepare("DELETE FROM presupuestos WHERE id_vendedor = ? ");
			$stm->execute(array($id_vendedor));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function CambiarEstado($data)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE presupuestos SET estado='Vendido' WHERE id_presupuesto = ? ");
			$stm->execute(array($data->id_presupuesto));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function Restaurar($data)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE presupuestos SET estado=null WHERE id_presupuesto = ? ");
			$stm->execute(array($data->id_presupuesto));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE presupuestos SET
						id_presupuesto     = ?,
						id_vendedor     = ?,
						id_producto     = ?,
						precio_venta   = ?,
                        cantidad      = ?, 
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
			$sql = "INSERT INTO presupuestos (id_presupuesto, id_cliente, id_vendedor, id_producto, precio_venta, cantidad, fecha_presupuesto, descuento, aprobado, estado, paciente) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_presupuesto,
						$data->id_cliente,
						$data->id_vendedor,
						$data->id_producto,
						$data->precio_venta,
						$data->cantidad,
						$data->fecha_presupuesto,
						$data->descuento,
						$data->aprobado,
						$data->estado,
						$data->paciente

					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function VentaAnulada($data)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE presupuestos SET estado='NULL', estado = 'Aprobado' WHERE id_presupuesto = ? ");
			$stm->execute(array($data->id_presupuesto));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Aprobar($id_presupuesto)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE presupuestos SET aprobado='si', estado='Aprobado' WHERE id_presupuesto = ? ");
			$stm->execute(array($id_presupuesto));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ActualizarEstado($id_presupuesto, $estado)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE presupuestos SET estado = ? WHERE id_presupuesto = ? ");
			$stm->execute(array($estado, $id_presupuesto));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerEstados()
	{
		try {
			$stm = $this->pdo->prepare("SELECT DISTINCT estado FROM presupuestos WHERE estado IS NOT NULL AND estado != '' ORDER BY estado");
			$stm->execute();
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarPorEstado($estado, $desde = '', $hasta = '')
	{
		try {
			if (!isset($_SESSION)) session_start();
			$userId = $_SESSION['user_id'];
			if (($_SESSION['nivel'] <> 3)) {
				$vendedor = "";
			} else {
				$vendedor = "AND v.id_vendedor = '$userId'";
			}

			// Agregar filtro de fechas si se proporcionan
			$filtroFecha = "";
			if (!empty($desde) && !empty($hasta)) {
				$filtroFecha = "AND CAST(v.fecha_presupuesto AS date) >= '$desde' AND CAST(v.fecha_presupuesto AS date) <= '$hasta'";
			} elseif (!empty($desde)) {
				$filtroFecha = "AND CAST(v.fecha_presupuesto AS date) >= '$desde'";
			} elseif (!empty($hasta)) {
				$filtroFecha = "AND CAST(v.fecha_presupuesto AS date) <= '$hasta'";
			}

			$result = array();

			$stm = $this->pdo->prepare("SELECT *, SUM((v.cantidad*v.precio_venta)-(((v.cantidad*v.precio_venta)*v.descuento))/100) AS total
			FROM presupuestos v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE v.estado = ? $vendedor $filtroFecha GROUP BY v.id_presupuesto ORDER BY v.fecha_presupuesto DESC");
			$stm->execute(array($estado));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Anular($id_presupuesto)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE presupuestos SET anulado=1 WHERE id_presupuesto = ? ");
			$stm->execute(array($id_presupuesto));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
