<?php
class transferencia_producto
{
	private $pdo;

	public $id;
	public $id_transferencia_producto;
	public $id_cliente;
	public $id_vendedor;
	public $id_producto;
	public $codigo;
	public $precio_venta;
	public $destino_transferencia;
	public $cantidad;
	public $fecha_transferencia_producto;
	public $fecha_confirmacion;
	public $anulado;
	public $descuento;
	public $tipo_transferencia;
	public $estado;

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
		try { //if (!isset($_SESSION)) session_start();
			$userId = $_SESSION['user_id'];
			if (($_SESSION['nivel'] == 1) || ($_SESSION['nivel'] == 4) ||  ($_SESSION['nivel'] == 2)) {
				$vendedor = "";
			} else {
				$vendedor = "AND v.id_vendedor = '$userId'";
			}

			$result = array();

			$stm = $this->pdo->prepare("SELECT *, SUM((v.cantidad*v.precio_venta)-(((v.cantidad*v.precio_venta)*v.descuento))/100) AS total
			FROM transferencia_productos v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE 1=1 $vendedor GROUP BY v.id_transferencia_producto ORDER BY v.id DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarDetalle($id_transferencia_producto)
	{
		try {

			$result = array();

			$stm = $this->pdo->prepare("SELECT *
											FROM transferencia_productos v 
												LEFT JOIN productos p ON v.id_producto = p.id
												LEFT JOIN usuario u ON v.id_vendedor = u.id
												LEFT JOIN clientes c ON v.id_cliente = c.id
											WHERE id_transferencia_producto = ? ORDER BY v.id DESC");
			$stm->execute(array($id_transferencia_producto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarDetalleRecibido($id_transferencia_producto)
	{ //LISTA LOS PRODUCTOS CONTENIDOS EN LA TRANSFERENCIA SELECCIONADA (CONECTANDOSE A LA DB DE TIENDA)
		try {
			$pdo_tienda = Database::StartUp_taller(); //linea para conectarse a la db de tienda

			$result = array();

			$stm = $pdo_tienda
				->prepare("SELECT *, m.marca AS marca
							FROM transferencia_productos v 
								LEFT JOIN productos p ON v.id_producto = p.id
								LEFT JOIN marcas m ON m.id = p.marca
								LEFT JOIN usuario u ON v.id_vendedor = u.id
								LEFT JOIN clientes c ON v.id_cliente = c.id
							WHERE id_transferencia_producto = ? ORDER BY v.id DESC");
			$stm->execute(array($id_transferencia_producto));

			$pdo_tienda = null; //cerrar la conexion con la db de tienda para evitar quedar conectado cuando no se usa
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function FinalizarTransferenciaRecibida($data)
	{ // MODIFICA LA TABLA DE LA BASE DE DATOS DE TIENDA PARA MARCAR COMO FINALIZADO SU TRANSFERENCIA
		try {
			$pdo_taller = Database::StartUp_taller();

			$fecha = date("Y-m-d H:i:s");
			$stm = $pdo_taller
				->prepare("UPDATE transferencia_productos SET estado='finalizado', fecha_confirmacion = '$fecha' WHERE id_transferencia_producto = ? ");
			$stm->execute(array($data));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function Listartransferencia_producto($id_transferencia_producto)
	{
		try {

			$result = array();

			$stm = $this->pdo->prepare("SELECT *, SUM((v.cantidad*v.precio_venta)-(((v.cantidad*v.precio_venta)*v.descuento))/100) AS total
			FROM transferencia_productos v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE id_transferencia_producto = ? ORDER BY v.id DESC");
			$stm->execute(array($id_transferencia_producto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarEnviadas($id_producto, $desde, $hasta)
	{
		try {
			$rango = ($desde == 0) ? "" : "AND tp.fecha_transferencia_producto >= '$desde' AND tp.fecha_transferencia_producto <= '$hasta'";
			$stm = $this->pdo->prepare("SELECT 	DATE_FORMAT(tp.fecha_transferencia_producto, '%d/%m/%y %H:%i') AS fecha_transferencia,
												DATE_FORMAT(tp.fecha_confirmacion, '%d/%m/%y %H:%i') AS fecha_confirmacion,
												tp.cantidad AS cantidad,
												u.user AS encargado,
												tp.id_transferencia_producto AS id_transf,
												tp.destino_transferencia AS destino,
												tp.anulado AS anulado,
												tp.estado AS estado
												FROM transferencia_productos tp 
												LEFT JOIN usuario u ON tp.id_vendedor = u.id
												WHERE tp.id_producto = ? $rango");
			$stm->execute(array($id_producto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarRecibidas($id_producto, $desde, $hasta)
	{
		try {
			$rango = ($desde == 0) ? "" : "AND tr.fecha_transferencia_producto >= '$desde' AND tr.fecha_transferencia_producto <= '$hasta'";
			$stm = $this->pdo->prepare("SELECT 	DATE_FORMAT(tr.fecha_transferencia_producto, '%d/%m/%y %H:%i') AS fecha_transferencia,
												DATE_FORMAT(tr.fecha_confirmacion, '%d/%m/%y %H:%i') AS fecha_confirmacion,
												tr.cantidad AS cantidad,
												u.user AS encargado,
												(SELECT u.user FROM usuario u WHERE tr.recibido_por = u.id LIMIT 1) AS recibido_por,
												tr.id_transferencia_producto AS id_transf,
												tr.destino_transferencia AS destino,
												tr.anulado AS anulado
												FROM transferencia_productos_recibidos tr 
												LEFT JOIN usuario u ON tr.id_vendedor = u.id
												WHERE tr.id_producto = ? $rango");
			$stm->execute(array($id_producto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ObtenerId_transferencia_producto($id)
	{
		try {
			$stm = $this->pdo->prepare("SELECT * FROM transferencia_productos WHERE id_transferencia_producto = ?");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarRecibir()
	{ //LISTA LAS TRANSFERENCIAS PENDIENTES EN LA TABLA TRANSFERENCIAS DE LA BASE DE DATOS DE TIENDA
		try { //if (!isset($_SESSION)) session_start();

			$pdo_tienda = Database::StartUp_taller();

			$userId = $_SESSION['user_id'];
			if (($_SESSION['nivel'] == 1) || ($_SESSION['nivel'] == 4)  ||  ($_SESSION['nivel'] == 2)) {
				$vendedor = "";
			} else {
				$vendedor = "AND t_p.id_vendedor = '$userId'";
			}

			$result = array();

			$stm = $pdo_tienda
				->prepare("SELECT *, 
								SUM((t_p.cantidad*t_p.precio_venta)-(((t_p.cantidad*t_p.precio_venta)*t_p.descuento))/100) AS total, 
								SUM(t_p.cantidad) AS cant_total
							FROM transferencia_productos t_p 
								LEFT JOIN productos p ON t_p.id_producto = p.id
								LEFT JOIN usuario u ON t_p.id_vendedor = u.id
								LEFT JOIN clientes c ON t_p.id_cliente = c.id
							WHERE 
								1=1 $vendedor 
								AND t_p.destino_transferencia = 'tienda_scp_1'
							GROUP BY t_p.id_transferencia_producto ORDER BY t_p.id DESC");
			$stm->execute(array());

			$pdo_tienda = null;
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
				->prepare("SELECT * FROM transferencia_productos  WHERE id_vendedor = '$user_id' GROUP BY id_transferencia_producto");


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
				->prepare("SELECT MAX(id_transferencia_producto) as id_transferencia_producto FROM transferencia_productos");
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
				->prepare("DELETE FROM transferencia_productos WHERE id = ?");

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
				->prepare("DELETE FROM transferencia_productos WHERE id_vendedor = ? ");
			$stm->execute(array($id_vendedor));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function CambiarEstado($data)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE transferencia_productos SET estado='Vendido' WHERE id_transferencia_producto = ? ");
			$stm->execute(array($data->id_transferencia_producto));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function CancelarTransferencia($data)
	{
		try {
			$fecha = date("Y-m-d H:i:s");
			$stm = $this->pdo
				->prepare("UPDATE transferencia_productos SET estado='cancelado' WHERE id_transferencia_producto = ? ");
			$stm->execute(array($data));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Restaurar($data)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE transferencia_productos SET estado=null WHERE id_transferencia_producto = ? ");
			$stm->execute(array($data->id_transferencia_producto));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE transferencia_productos SET
						id_transferencia_producto     = ?,
						id_vendedor     = ?,
						id_producto     = ?,
						codigo     = ?,
						precio_venta   = ?,
                        cantidad      = ?, 
						fecha_transferencia_producto      = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_transferencia_producto,
						$data->id_vendedor,
						$data->id_producto,
						$data->codigo,
						$data->precio_venta,
						$data->cantidad,
						$data->fecha_transferencia_producto,
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
			$sql = "INSERT INTO transferencia_productos (id_transferencia_producto, id_cliente, destino_transferencia, id_vendedor, id_producto, codigo, precio_venta, cantidad, fecha_transferencia_producto, descuento, tipo_transferencia, estado) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_transferencia_producto,
						$data->id_cliente,
						$data->destino_transferencia,
						$data->id_vendedor,
						$data->id_producto,
						$data->codigo,
						$data->precio_venta,
						$data->cantidad,
						$data->fecha_transferencia_producto,
						$data->descuento,
						$data->tipo_transferencia,
						$data->estado

					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	
	public function RegistrarRecibidos($data)
	{
		try {
			$sql = "INSERT INTO transferencia_productos_recibidos (id_transferencia_producto, id_cliente, destino_transferencia, id_vendedor, recibido_por, id_producto, codigo, precio_venta, cantidad, fecha_confirmacion, fecha_transferencia_producto, descuento, tipo_transferencia, estado) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_transferencia_producto,
						$data->id_cliente,
						$data->destino_transferencia,
						$data->id_vendedor,
						$data->recibido_por,
						$data->id_producto,
						$data->codigo,
						$data->precio_venta,
						$data->cantidad,
						$data->fecha_confirmacion,
						$data->fecha_transferencia_producto,
						$data->descuento,
						$data->tipo_transferencia,
						$data->estado
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
