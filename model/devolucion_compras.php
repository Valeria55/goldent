<?php
class devolucion_compras
{
	private $pdo;

	public $id;
	public $id_compra;
	public $id_cliente;
	public $id_vendedor;
	public $vendedor_salon;
	public $id_producto;
	public $id_deuda;
	public $precio_costo;
	public $precio_compra;
	public $subtotal;
	public $descuento;
	public $total;
	public $comprobante;
	public $nro_comprobante;
	public $cantidad;
	public $margen_ganancia;
	public $fecha_compra;
	public $metodo;
	public $contado;
	public $motivo;

	public function __CONSTRUCT()
	{
		try {
			$this->pdo = Database::StartUp();
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Listar($id_compra = 0)
	{
		try {

			if ($id_compra == 0) {

				$stm = $this->pdo->prepare("SELECT v.id, v.id_compra, v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) AS subtotal, descuento, 
					SUM(total) as total, AVG(margen_ganancia) AS margen_ganancia, fecha_compra, nro_comprobante, 
					c.nombre AS nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, 
					(SELECT user FROM usuario WHERE id = v.id_vendedor) AS vendedor, 
					(SELECT user FROM usuario WHERE id = v.vendedor_salon) AS vendedor_salon
					FROM devoluciones_compras v 
					LEFT JOIN productos p ON v.id_producto = p.id 
					LEFT JOIN clientes c ON v.id_cliente = c.id 
					GROUP BY v.id_compra ORDER BY v.id_compra DESC");
				$stm->execute();
			} else {
				$stm = $this->pdo->prepare("SELECT v.id, p.producto,v.comprobante, v.metodo, 
					v.anulado, contado, p.codigo,p.iva, v.cantidad, v.precio_compra, v.motivo,
					subtotal, descuento, total, margen_ganancia, 
					fecha_compra, nro_comprobante, c.nombre AS nombre_cli,
				 c.ruc, c.direccion, c.telefono, v.id_producto 
				 FROM devoluciones_compras v 
				 LEFT JOIN productos p ON v.id_producto = p.id 
				 LEFT JOIN clientes c ON v.id_cliente = c.id 
				 WHERE v.id_compra = ?");
				$stm->execute(array($id_compra));
			}

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarDevolucionesCom($id_producto, $desde, $hasta)
	{
		try {
			$rango = ($desde == 0) ? "" : "AND dc.fecha_compra >= '$desde' AND dc.fecha_compra <= '$hasta'";
			$stm = $this->pdo->prepare("SELECT	dc.anulado AS anulado,
												u.user AS nombre_vendedor,
												dc.motivo AS motivo,
												DATE_FORMAT(dc.fecha_compra, '%d/%m/%y %H:%i') AS fecha_compra,
												dc.precio_compra AS precio_compra,
												dc.cantidad AS cantidad_com
											FROM devoluciones_compras dc
											LEFT JOIN usuario u ON dc.id_vendedor = u.id
											WHERE dc.id_producto = ? $rango");
			$stm->execute(array($id_producto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarId_compra()
	{
		try {

			$stm = $this->pdo->prepare("SELECT d.id AS id, id_compra, c.nombre, c.ruc  
				FROM devoluciones_compras d
				LEFT JOIN clientes c ON d.id_cliente = c.id
				GROUP BY d.id_compra ORDER BY d.id_compra DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function AgrupadoProducto($fecha)
	{
		try {

			$stm = $this->pdo->prepare("SELECT p.producto, SUM(v.cantidad) as cantidad, SUM(v.total) as total, SUM(v.cantidad*v.precio_costo) as costo, v.id_cliente FROM devoluciones_compras v 
				LEFT JOIN productos p ON v.id_producto = p.id 
				LEFT JOIN clientes c ON v.id_cliente = c.id 
				WHERE MONTH(fecha_compra) = MONTH(?) AND YEAR(fecha_compra) = YEAR(?) AND anulado = 0 
				OR (MONTH(fecha_compra) = '7' AND DAY(fecha_compra) = '1' AND anulado = 0) 
				GROUP BY v.id_producto 
				ORDER BY v.id_compra DESC");
			$stm->execute(array($fecha, $fecha));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarProducto($id_producto)
	{
		try {


			$stm = $this->pdo->prepare("SELECT v.id, p.producto,v.comprobante, v.metodo, v.anulado, contado, p.codigo,p.iva, v.cantidad, v.precio_costo, v.precio_compra, subtotal, descuento, total, margen_ganancia, fecha_compra, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, (SELECT user FROM usuario u WHERE u.id = v.id_vendedor) AS vendedor,
				(SELECT user FROM usuario WHERE id = v.vendedor_salon) AS vendedor_salon 
				FROM devoluciones_compras v LEFT JOIN productos p ON v.id_producto = p.id 
				LEFT JOIN clientes c ON v.id_cliente = c.id 
				WHERE v.id_producto = ?");
			$stm->execute(array($id_producto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarCliente($id_cliente)
	{
		try {


			$stm = $this->pdo->prepare("SELECT v.id, v.id_compra, v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) AS subtotal, descuento, SUM(total) AS total, 
				AVG(margen_ganancia) AS margen_ganancia, fecha_compra, nro_comprobante, 
				c.nombre AS nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, 
				(SELECT user FROM usuario WHERE id = v.id_vendedor) AS vendedor, 
				(SELECT user FROM usuario WHERE id = v.vendedor_salon) AS vendedor_salon 
				FROM devoluciones_compras v 
				LEFT JOIN productos p ON v.id_producto = p.id 
				LEFT JOIN clientes c ON v.id_cliente = c.id 
				WHERE id_cliente = ? 
				GROUP BY v.id_compra 
				ORDER BY v.id_compra DESC");
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
			$stm = $this->pdo->prepare("SELECT v.id, v.id_compra, v.comprobante, v.metodo, v.anulado, contado, p.producto, SUM(subtotal) AS subtotal, descuento, SUM(total) AS total, 
				AVG(margen_ganancia) AS margen_ganancia, fecha_compra, nro_comprobante, c.nombre AS nombre_cli,
				c.ruc, c.direccion, c.telefono, v.id_producto, 
				(SELECT user FROM usuario WHERE id = v.id_vendedor) AS vendedor, 
				(SELECT user FROM usuario WHERE id = v.vendedor_salon) AS vendedor_salon, 
				(SELECT comision FROM usuario WHERE id = v.id_vendedor) AS comision  
				FROM devoluciones_compras v 
				LEFT JOIN productos p ON v.id_producto = p.id 
				LEFT JOIN clientes c ON v.id_cliente = c.id 
				WHERE vendedor_salon = ? AND MONTH(fecha_compra) = MONTH(?) AND YEAR(fecha_compra) = YEAR(?) 
				AND anulado = '0' 
				GROUP BY v.id_compra 
				ORDER BY v.id_compra DESC");
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
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_compra, a.nombre AS nombre_cli, v.anulado, c.producto, SUM(subtotal) AS subtotal, v.descuento, SUM(v.total) AS total, 
				AVG(margen_ganancia) AS margen_ganancia, fecha_compra, nro_comprobante, v.id_producto, 
				(SELECT user FROM usuario WHERE id = v.id_vendedor) AS vendedor, 
				(SELECT user FROM usuario WHERE id = v.vendedor_salon) AS vendedor_salon 
				FROM devoluciones_compras v 
				LEFT JOIN productos c ON v.id_producto = c.id 
				LEFT JOIN clientes a ON v.id_cliente = a.id 
				WHERE CAST(v.fecha_compra AS date) = ? $usuario  
				GROUP BY v.id_compra DESC");
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
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_compra, a.nombre AS nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.total) AS total, 
				AVG(margen_ganancia) as margen_ganancia, fecha_compra, nro_comprobante, v.id_producto, 
				(SELECT user FROM usuario WHERE id = v.id_vendedor) AS vendedor, 
				(SELECT user FROM usuario WHERE id = v.vendedor_salon) AS vendedor_salon 
				FROM devoluciones_compras v 
				LEFT JOIN productos c ON v.id_producto = c.id 
				LEFT JOIN clientes a ON v.id_cliente = a.id 
				WHERE fecha_compra >= (SELECT fecha_apertura FROM cierres WHERE id_usuario = ? AND fecha_cierre IS NULL) $usuario  GROUP BY v.id_compra DESC");
			$stm->execute(array($id_vendedor));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarDiaSinAnular($fecha)
	{
		try {

			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_compra, a.nombre AS nombre_cli, v.anulado, c.producto, SUM(subtotal) AS subtotal, v.descuento, SUM(v.precio_costo * v.cantidad) AS costo, SUM(v.total) AS total, AVG(margen_ganancia) AS margen_ganancia, fecha_compra, nro_comprobante,
			    v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) AS vendedor, 
			    (SELECT user FROM usuario WHERE id = v.vendedor_salon) AS vendedor_salon 
			    FROM devoluciones_compras v 
			    LEFT JOIN productos c ON v.id_producto = c.id 
			    LEFT JOIN clientes a ON v.id_cliente = a.id 
			    WHERE CAST(v.fecha_compra AS date) = ? AND anulado <> 1  
			    GROUP BY v.id_compra DESC");
			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarRango($desde, $hasta)
	{
		try {

			$stm = $this->pdo->prepare("SELECT v.id_cliente, v.metodo, v.contado, v.id_compra, 
				a.nombre AS nombre_cli, v.anulado, c.producto, SUM(subtotal) AS subtotal, 
				v.descuento, SUM(v.precio_costo * v.cantidad) AS costo, SUM(v.total) AS total, 
				AVG(margen_ganancia) AS margen_ganancia, fecha_compra, nro_comprobante, v.id_producto, 
				(SELECT user FROM usuario WHERE id = v.id_vendedor) AS vendedor, 
				(SELECT user FROM usuario WHERE id = v.vendedor_salon) AS vendedor_salon 
				FROM devoluciones_compras v 
				LEFT JOIN productos c ON v.id_producto = c.id 
				LEFT JOIN clientes a ON v.id_cliente = a.id 
				WHERE CAST(v.fecha_compra AS date) >= ? AND CAST(v.fecha_compra AS date) <= ? 
				AND anulado <> 1  
				GROUP BY v.id_compra DESC");
			$stm->execute(array($desde, $hasta));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarUsados($desde, $hasta)
	{
		try {

			$stm = $this->pdo->prepare("SELECT c.producto, v.fecha_compra, v.precio_costo, v.precio_compra, 
				v.cantidad, (v.precio_compra*v.cantidad) AS total 
				FROM devoluciones_compras v 
				LEFT JOIN productos c ON v.id_producto = c.id 
				WHERE CAST(v.fecha_compra AS date) >= ? AND CAST(v.fecha_compra AS date) <= ? 
				AND anulado <> 1 AND v.id_cliente = 14 
				ORDER BY v.id DESC");
			$stm->execute(array($desde, $hasta));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarRangoSinAnular($desde, $hasta, $id_vendedor)
	{
		try {

			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_compra, a.nombre AS nombre_cli,
			 v.anulado, c.producto, SUM(subtotal) AS subtotal, v.descuento, 
			 SUM(v.precio_costo * v.cantidad) AS costo, SUM(v.total) AS total, 
			 AVG(margen_ganancia) AS margen_ganancia, fecha_compra, nro_comprobante, v.id_producto, 
			 (SELECT user FROM usuario WHERE id = v.id_vendedor) AS vendedor, 
			 (SELECT user FROM usuario WHERE id = v.vendedor_salon) AS vendedor_salon 
			 FROM devoluciones_compras v 
			 LEFT JOIN productos c ON v.id_producto = c.id 
			 LEFT JOIN clientes a ON v.id_cliente = a.id 
			 WHERE fecha_compra >= ? AND fecha_compra <= ?  AND anulado <> 1 AND id_vendedor = ?  
			 GROUP BY v.id_compra DESC");
			$stm->execute(array($desde, $hasta, $id_vendedor));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarMesSinAnular($fecha)
	{
		try {
			$stm = $this->pdo->prepare("SELECT v.id_cliente, v.metodo, v.contado, v.id_compra, 
				a.nombre AS nombre_cli, v.anulado, c.producto, SUM(subtotal) AS subtotal,
				v.descuento, SUM(v.precio_costo * v.cantidad) AS costo, SUM(v.total) AS total, 
				AVG(margen_ganancia) AS margen_ganancia, fecha_compra, nro_comprobante, v.id_producto, 
				(SELECT user FROM usuario WHERE id = v.id_vendedor) AS vendedor, 
				(SELECT user FROM usuario WHERE id = v.vendedor_salon) AS vendedor_salon 
				FROM devoluciones_compras v 
				LEFT JOIN productos c ON v.id_producto = c.id 
				LEFT JOIN clientes a ON v.id_cliente = a.id 
				WHERE MONTH(v.fecha_compra) = MONTH(?) AND YEAR(v.fecha_compra) = YEAR(?) AND anulado <> 1  
				GROUP BY v.id_compra DESC");
			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarDiaContado($fecha)
	{
		try {

			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_compra, cli.Nombre AS nombre_cli,
			 c.producto, SUM(subtotal) AS subtotal, v.descuento, SUM(v.total) AS total, 
			 AVG(margen_ganancia) AS margen_ganancia, fecha_compra, nro_comprobante 
			 FROM devoluciones_compras v 
			 LEFT JOIN productos c ON v.id_producto = c.id 
			 LEFT JOIN clientes cli ON v.id_cliente = cli.id 
			 WHERE CAST(v.fecha_compra AS date) = ? AND contado = 'contado' 
			 GROUP BY v.id_compra DESC");
			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarMes($fecha)
	{
		try {

			$stm = $this->pdo->prepare("SELECT v.id_compra, cli.Nombre AS nombre_cli, v.metodo, c.producto, 
				SUM(subtotal) AS subtotal, descuento, SUM(total) AS total, AVG(margen_ganancia) AS ganancia, fecha_compra, nro_comprobante 
				FROM devoluciones_compras v 
				LEFT JOIN productos c ON v.id_producto = c.id 
				LEFT JOIN clientes cli ON cli.id = v.id_cliente  
				WHERE MONTH(v.fecha_compra) = MONTH(?) AND YEAR(v.fecha_compra) = YEAR(?) 
				GROUP BY v.id_compra DESC");
			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Detalles($id_compra)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT c.producto, subtotal, descuento, total, margen_ganancia, fecha_compra, nro_comprobante 
				FROM devoluciones_compras v 
				JOIN productos c ON v.id_producto = c.id 
				WHERE v.id_compra = ?");
			$stm->execute(array($id_compra));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Obtener($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM devoluciones_compras WHERE id = ?");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerProducto($id_compra, $id_producto)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM devoluciones_compras WHERE id_compra = ? AND id_producto = ?");


			$stm->execute(array($id_compra, $id_producto));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerUNO($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM devoluciones_compras WHERE id_compra = ? LIMIT 1");


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
				->prepare("SELECT * FROM devoluciones_compras WHERE id_compra = ?");


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
				->prepare("SELECT MAX(id) as id FROM devoluciones_compras");
			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Cantidad($id_item, $id_compra, $cantidad)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE devoluciones_compras SET cantidad = ?, subtotal = precio_compra * ?, 
			          	total = precio_compra * ? WHERE id = ?");
			$stm->execute(array($cantidad, $cantidad, $cantidad, $id_item));
			$stm = $this->pdo
				->prepare("SELECT *, (SELECT SUM(total) 
			          	FROM devoluciones_compras WHERE id_compra = ? 
			          	GROUP BY id_compra) AS total_compra 
			          	FROM devoluciones_compras WHERE id = ?");
			$stm->execute(array($id_compra, $id_item));

			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function CancelarItem($id_item)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM devoluciones_compras WHERE id = ?");

			$stm->execute(array($id_item));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Eliminar($id)
	{
		try {
			$stm = $this->pdo
				->prepare("DELETE FROM devoluciones_compras WHERE id_compra = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Anular($id)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE devoluciones_compras SET anulado = 1 WHERE id_compra = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE devoluciones_compras SET
						id_compra     = ?,
						id_vendedor     = ?,
						id_producto     = ?,
						id_deuda        = ?,
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

	public function Registrar($data)
	{
		try {



			$sql = "INSERT INTO devoluciones_compras (id_compra, id_cliente, id_vendedor, vendedor_salon, id_producto, id_deuda, precio_costo, precio_compra, subtotal, descuento, iva, total, comprobante, nro_comprobante, cantidad, margen_ganancia, fecha_compra, metodo, banco, contado, motivo) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->id_compra,
						$data->id_cliente,
						$data->id_vendedor,
						$data->vendedor_salon,
						$data->id_producto,
						$data->id_deuda,
						$data->precio_costo,
						$data->precio_compra,
						$data->subtotal,
						$data->descuento,
						$data->iva,
						$data->total,
						$data->comprobante,
						$data->nro_comprobante,
						$data->cantidad,
						$data->margen_ganancia,
						$data->fecha_compra,
						$data->metodo,
						$data->banco,
						$data->contado,
						$data->motivo

					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
