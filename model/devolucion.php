<?php
class devolucion
{
	private $pdo;

	public $id;
	public $id_venta;
	public $id_cliente;
	public $id_vendedor;
	public $vendedor_salon;
	public $id_producto;
	public $precio_costo;
	public $precio_venta;
	public $subtotal;
	public $descuento;
	public $total;
	public $comprobante;
	public $nro_comprobante;
	public $cantidad;
	public $margen_ganancia;
	public $fecha_venta;
	public $metodo;
	public $contado;
	public $iva;
	public $banco;
	public $venta;

	public function __CONSTRUCT()
	{
		try {
			$this->pdo = Database::StartUp();
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Listar($id_venta)
	{
		try {

			if ($id_venta == 0) {

				$stm = $this->pdo->prepare("SELECT 
				v.venta, 
				v.id, 
				v.id_venta, 
				v.comprobante, 
				v.anulado,
				SUM(subtotal) as subtotal, 
				SUM(total) as total, 
				fecha_venta,
				IFNULL((SELECT SUM(ve.total) FROM ventas ve WHERE ve.id_venta = v.venta AND ve.anulado = 0), 0) as monto_venta,
				(SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor
				FROM devoluciones v 
				LEFT JOIN clientes c ON v.id_cliente = c.id 
				GROUP BY v.id_venta ORDER BY v.id_venta DESC");
				$stm->execute();
			} else {
				$stm = $this->pdo->prepare("SELECT v.id, p.producto,v.comprobante, v.metodo, v.anulado, contado, p.codigo,p.iva, v.cantidad, v.precio_venta, subtotal, descuento, total, margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto FROM devoluciones v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id WHERE v.id_venta = ?");
				$stm->execute(array($id_venta));
			}

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function AgrupadoProducto($fecha)
	{
		try {

			$stm = $this->pdo->prepare("SELECT p.producto, SUM(v.cantidad) as cantidad, SUM(v.total) as total, SUM(v.cantidad*v.precio_costo) as costo, v.id_cliente FROM devoluciones v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id WHERE MONTH(fecha_venta) = MONTH(?) AND YEAR(fecha_venta) = YEAR(?) AND anulado = 0 OR (MONTH(fecha_venta) = '7' AND DAY(fecha_venta) = '1' AND anulado = 0) GROUP BY v.id_producto ORDER BY v.id_venta DESC");
			$stm->execute(array($fecha, $fecha));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarProducto($id_producto)
	{
		try {


			$stm = $this->pdo->prepare("SELECT v.id, p.producto,v.comprobante, v.metodo, v.anulado, contado, p.codigo,p.iva, v.cantidad, v.precio_costo, v.precio_venta, subtotal, descuento, total, margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, (SELECT user FROM usuario u WHERE u.id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM devoluciones v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id WHERE v.id_producto = ?");
			$stm->execute(array($id_producto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarInOut($id_producto, $desde, $hasta)
	{
		try {

			$rango = ($desde == 0) ? "" : "AND d.fecha_venta >= '$desde' AND d.fecha_venta <= '$hasta'";
			$stm = $this->pdo->prepare("SELECT d.cantidad AS cantidad, DATE_FORMAT(d.fecha_venta, '%d/%m/%y %H:%i') AS fecha_venta, d.anulado AS anulado, u.user AS user FROM devoluciones d LEFT JOIN usuario u ON d.id_vendedor = u.id WHERE d.id_producto = ? $rango");
			$stm->execute(array($id_producto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	
	public function ListarCliente($id_cliente)
	{
		try {


			$stm = $this->pdo->prepare("SELECT v.id, v.id_venta, v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM devoluciones v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id WHERE id_cliente = ? GROUP BY v.id_venta ORDER BY v.id_venta DESC");
			$stm->execute(array($id_cliente));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarUsuarioMes($id_usuario, $mes)
	{
		try {

			$fecha = $mes . "-10";
			$stm = $this->pdo->prepare("SELECT v.id, v.id_venta, v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon, (SELECT comision FROM usuario WHERE id = v.id_vendedor) as comision  FROM devoluciones v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id WHERE vendedor_salon = ? AND MONTH(fecha_venta) = MONTH(?) AND YEAR(fecha_venta) = YEAR(?) AND anulado = '0' GROUP BY v.id_venta ORDER BY v.id_venta DESC");
			$stm->execute(array($id_usuario, $fecha, $fecha));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarDia($fecha)
	{
		try {
			if (!isset($_SESSION['user_id'])) {
				if (!isset($_SESSION)) session_start();
			}
			$id_vendedor = $_SESSION['user_id'];
			$usuario = ($_SESSION['nivel'] == 1) ? "" : "AND v.id_vendedor = $id_vendedor";
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM devoluciones v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE CAST(v.fecha_venta AS date) = ? $usuario  GROUP BY v.id_venta DESC");
			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarDiaSesion()
	{
		try {
			if (!isset($_SESSION['user_id'])) {
				if (!isset($_SESSION)) session_start();
			}
			$id_vendedor = $_SESSION['user_id'];
			$usuario = ($_SESSION['nivel'] == 1) ? "" : "AND v.id_vendedor = $id_vendedor";
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM devoluciones v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE fecha_venta >= (SELECT fecha_apertura FROM cierres WHERE id_usuario = ? AND fecha_cierre IS NULL) $usuario  GROUP BY v.id_venta DESC");
			$stm->execute(array($id_vendedor));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarDiaSinAnular($fecha)
	{
		try {

			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.precio_costo * v.cantidad) as costo, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM devoluciones v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE CAST(v.fecha_venta AS date) = ? AND anulado <> 1  GROUP BY v.id_venta DESC");
			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarRango($desde, $hasta)
	{
		try {

			$stm = $this->pdo->prepare("SELECT v.id_cliente, v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.precio_costo * v.cantidad) as costo, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM devoluciones v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE CAST(v.fecha_venta AS date) >= ? AND CAST(v.fecha_venta AS date) <= ? AND anulado <> 1  GROUP BY v.id_venta DESC");
			$stm->execute(array($desde, $hasta));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarUsados($desde, $hasta)
	{
		try {

			$stm = $this->pdo->prepare("SELECT c.producto, v.fecha_venta, v.precio_costo, v.precio_venta, v.cantidad, (v.precio_venta*v.cantidad) as total FROM devoluciones v LEFT JOIN productos c ON v.id_producto = c.id WHERE CAST(v.fecha_venta AS date) >= ? AND CAST(v.fecha_venta AS date) <= ? AND anulado <> 1 AND v.id_cliente = 14 ORDER BY v.id DESC");
			$stm->execute(array($desde, $hasta));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarRangoSinAnular($desde, $hasta, $id_vendedor)
	{
		try {

			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.precio_costo * v.cantidad) as costo, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM devoluciones v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE fecha_venta >= ? AND fecha_venta <= ?  AND anulado <> 1 AND id_vendedor = ?  GROUP BY v.id_venta DESC");
			$stm->execute(array($desde, $hasta, $id_vendedor));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarMesSinAnular($fecha)
	{
		try {
			$stm = $this->pdo->prepare("SELECT v.id_cliente, v.metodo, v.contado, v.id_venta, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.precio_costo * v.cantidad) as costo, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor, (SELECT user FROM usuario WHERE id = v.vendedor_salon) as vendedor_salon FROM devoluciones v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE MONTH(v.fecha_venta) = MONTH(?) AND YEAR(v.fecha_venta) = YEAR(?) AND anulado <> 1  GROUP BY v.id_venta DESC");
			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarDiaContado($fecha)
	{
		try {

			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_venta, cli.Nombre as nombre_cli, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_venta, nro_comprobante FROM devoluciones v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes cli ON v.id_cliente = cli.id WHERE CAST(v.fecha_venta AS date) = ? AND contado = 'contado' GROUP BY v.id_venta DESC");
			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarMes($fecha)
	{
		try {

			$stm = $this->pdo->prepare("SELECT v.id_venta, cli.Nombre AS nombre_cli, v.metodo, c.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as ganancia, fecha_venta, nro_comprobante FROM devoluciones v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes cli ON cli.id = v.id_cliente  WHERE MONTH(v.fecha_venta) = MONTH(?) AND YEAR(v.fecha_venta) = YEAR(?) GROUP BY v.id_venta DESC");
			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Detalles($id_venta)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT c.producto, subtotal, descuento, total, margen_ganancia, fecha_venta, nro_comprobante FROM devoluciones v JOIN productos c ON v.id_producto = c.id WHERE v.id_venta = ?");
			$stm->execute(array($id_venta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Obtener($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM devoluciones WHERE id = ?");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerProducto($id_venta, $id_producto)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM devoluciones WHERE id_venta = ? AND id_producto = ?");


			$stm->execute(array($id_venta, $id_producto));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerUNO($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM devoluciones WHERE id_venta = ? LIMIT 1");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Recibo($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM devoluciones WHERE id_venta = ?");


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
				->prepare("SELECT MAX(id_venta) as id_venta FROM devoluciones");
			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Cantidad($id_item, $id_venta, $cantidad)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE devoluciones SET cantidad = ?, subtotal = precio_venta * ?, total = precio_venta * ? WHERE id = ?");
			$stm->execute(array($cantidad, $cantidad, $cantidad, $id_item));
			$stm = $this->pdo
				->prepare("SELECT *, (SELECT SUM(total) FROM devoluciones WHERE id_venta = ? GROUP BY id_venta) as total_venta FROM devoluciones WHERE id = ?");
			$stm->execute(array($id_venta, $id_item));

			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function CancelarItem($id_item)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM devoluciones WHERE id = ?");

			$stm->execute(array($id_item));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Eliminar($id)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM devoluciones WHERE id_venta = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Anular($id)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE devoluciones SET anulado = 1 WHERE id_venta = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE devoluciones SET
						id_venta     = ?,
						id_vendedor     = ?,
						id_producto     = ?,
						precio_venta   = ?,
                        cantidad      = ?, 
						margen_ganancia     = ?,
						fecha_venta      = ?
						
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
						$data->id
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Registrar($data)
	{
		try {



			$sql = "INSERT INTO devoluciones (id_venta, id_cliente, id_vendedor, vendedor_salon, id_producto, precio_costo, precio_venta, subtotal, descuento, iva, total, comprobante, nro_comprobante, cantidad, margen_ganancia, fecha_venta, metodo, banco, contado, venta) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_venta,
						$data->id_cliente,
						$data->id_vendedor,
						$data->vendedor_salon,
						$data->id_producto,
						$data->precio_costo,
						$data->precio_venta,
						$data->subtotal,
						$data->descuento,
						$data->iva,
						$data->total,
						$data->comprobante,
						$data->nro_comprobante,
						$data->cantidad,
						$data->margen_ganancia,
						$data->fecha_venta,
						$data->metodo,
						$data->banco,
						$data->contado,
						$data->venta

					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
