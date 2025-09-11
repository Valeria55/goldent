<?php
class producto

{
	private $pdo;

	public $id;
	public $codigo;
	public $id_categoria;
	public $producto;
	public $marca;
	public $descripcion;
	public $precio_costo;
	public $precio_minorista;
	public $precio_mayorista;
	public $stock;
	public $stock_minimo;
	public $descuento_max;
	public $importado;
	public $iva;
	public $sucursal;
	public $anulado;
	public $preciob;
	public $tipo;


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
			if (!isset($_SESSION['nivel'])) {
				if (!isset($_SESSION)) session_start();
			}
			if (true) {
				$sucursal = "WHERE p.sucursal = " . $_SESSION['sucursal'];
			} else {
				$sucursal = "";
			}


			$stm = $this->pdo->prepare("SELECT *, p.id, s.sucursal 
			      FROM productos p 
			      LEFT JOIN categorias c ON p.id_categoria = c.id
			      LEFT JOIN sucursales s ON p.sucursal = s.id 
			       LEFT JOIN marcas m ON m.id = p.marca  
			      WHERE p.anulado IS NULL AND p.tipo = 'producto'
			      ORDER BY p.id ASC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function ListarCompraProducto($id_venta)
	{
		try {

			$stm = $this->pdo->prepare("
				SELECT * FROM compras v
				LEFT JOIN productos p ON v.id_producto = p.id
				WHERE id_compra = ?
				ORDER BY p.id");
			$stm->execute(array($id_venta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarStockTiempo($fecha)
	{
		try {


			$hoy = date('Y-m-d');
			$fecha = $fecha != '' ? $fecha : $hoy;

			$stm = $this->pdo->prepare(
				"WITH compras AS (
                SELECT id_producto, SUM(cantidad) AS total_comprado
                FROM compras
                WHERE CAST(fecha_compra AS date) <= '$fecha' AND anulado = 0
                GROUP BY id_producto
            ), devoluciones_compras AS (
                SELECT id_producto, SUM(cantidad) AS total_devuelto_compra
                FROM devoluciones_compras
                WHERE CAST(fecha_compra AS date) <= '$fecha' AND anulado = 0
                GROUP BY id_producto
            ), devoluciones_ventas AS (
                SELECT id_producto, SUM(cantidad) AS total_devuelto_venta
                FROM devoluciones_ventas
                WHERE CAST(fecha_venta AS date) <= '$fecha' AND anulado = 0
                GROUP BY id_producto
            ), devoluciones AS (
                SELECT id_producto, SUM(cantidad) AS total_ajustes
                FROM devoluciones
                WHERE CAST(fecha_venta AS date) <= '$fecha' AND anulado = 0
                GROUP BY id_producto
            ), transferencia_productos AS (
                SELECT id_producto, SUM(cantidad) AS total_enviado
                FROM transferencia_productos
                WHERE CAST(fecha_confirmacion AS date) <= '$fecha' AND anulado = 0 AND estado<>'cancelado'
                GROUP BY id_producto
            ), transferencia_productos_recibidos AS (
                SELECT id_producto, SUM(cantidad) AS total_recibido
                FROM transferencia_productos_recibidos
                WHERE CAST(fecha_confirmacion AS date) <= '$fecha' AND anulado = 0 
                GROUP BY id_producto
            ),ventas AS (
                SELECT id_producto, SUM(cantidad) AS total_ventas
                FROM ventas
                WHERE CAST(fecha_venta AS date) <= '$fecha' AND anulado = 0
                GROUP BY id_producto
			)
            SELECT
                p.codigo,
                p.producto,
                p.id,
                p.precio_costo,
                COALESCE(c.total_comprado, 0) - COALESCE(dc.total_devuelto_compra, 0) -
                COALESCE(dv.total_devuelto_venta, 0) + COALESCE(a.total_ajustes, 0) +
                COALESCE(tr.total_recibido, 0) - COALESCE(te.total_enviado, 0) - COALESCE(v.total_ventas, 0)  AS stock_total
            FROM productos p
            LEFT JOIN devoluciones_compras dc ON p.id = dc.id_producto
            LEFT JOIN devoluciones_ventas dv ON p.id = dv.id_producto
            LEFT JOIN devoluciones a ON p.id= a.id_producto
            LEFT JOIN transferencia_productos te ON p.id = te.id_producto
            LEFT JOIN transferencia_productos_recibidos tr ON p.id = tr.id_producto
			LEFT JOIN ventas v ON p.id= v.id_producto
            LEFT JOIN compras c ON p.id=c.id_producto 
            WHERE p.anulado IS NULL;"

			);

			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarAjax()
	{
		try {

			$stm = $this->pdo->prepare("SELECT *, p.id, s.sucursal, sub.categoria AS sub_categoria, sub.id_padre, m.marca 
			FROM productos p 
			LEFT JOIN categorias sub ON p.id_categoria = sub.id 
			LEFT JOIN categorias c ON sub.id_padre = c.id 
			LEFT JOIN sucursales s ON p.sucursal = s.id 
			LEFT JOIN marcas m ON m.id = p.marca 
			WHERE p.anulado IS NULL AND p.tipo = 'producto' ORDER BY p.id ASC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarServicios()
	{
		try {

			$stm = $this->pdo->prepare("SELECT *, p.id, s.sucursal, sub.categoria AS sub_categoria, sub.id_padre, m.marca 
			FROM productos p 
			LEFT JOIN categorias sub ON p.id_categoria = sub.id 
			LEFT JOIN categorias c ON sub.id_padre = c.id 
			LEFT JOIN sucursales s ON p.sucursal = s.id 
			LEFT JOIN marcas m ON m.id = p.marca 
			WHERE p.anulado IS NULL AND p.tipo = 'servicio' ORDER BY p.id ASC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function consultarVenta($id_venta)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT SUM(monto) as total FROM ingresos WHERE id_venta = ?");
			$stm->execute(array($id_venta));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarVenta($id_venta)
	{
		try {

			$stm = $this->pdo->prepare("SELECT * FROM productos WHERE id IN (SELECT id_producto FROM ventas WHERE id_venta = ?) AND id NOT IN (SELECT id_producto FROM devoluciones_tmp) ORDER BY id DESC");
			$stm->execute(array($id_venta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarBuscar($q)
	{
		try {
			$result = array();
			if (!isset($_SESSION['nivel'])) {
				if (!isset($_SESSION)) session_start();
			}
			if ($_SESSION['nivel'] != 1) {
				$sucursal = "WHERE p.sucursal = " . $_SESSION['sucursal'];
			} else {
				$sucursal = "";
			}

			if ($q != "") {
				$sucursal = "AND p.sucursal = " . $q;
			} else {
				$sucursal = "";
			}

			$stm = $this->pdo->prepare("SELECT *, p.id, s.sucursal FROM productos p JOIN categorias c ON p.id_categoria = c.id LEFT JOIN sucursales s ON p.sucursal = s.id JOIN marcas m ON m.id = p.marca $sucursal  ORDER BY p.id DESC LIMIT 50");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarVentaProducto($id_venta)
	{
		try {

			$stm = $this->pdo->prepare("
				SELECT * FROM ventas v
				LEFT JOIN productos p ON v.id_producto = p.id
				WHERE id_venta = ?
				ORDER BY p.id");
			$stm->execute(array($id_venta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarTodo()
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, p.id, s.sucursal, p.sucursal as id_sucursal 
			FROM productos p 
			JOIN categorias c ON p.id_categoria = c.id 
			LEFT JOIN sucursales s ON p.sucursal = s.id 
			JOIN marcas m ON m.id = p.marca ORDER BY p.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ListarTodoBalance()
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, p.id, s.sucursal, p.sucursal as id_sucursal 
			FROM productos p 
			JOIN categorias c ON p.id_categoria = c.id 
			LEFT JOIN sucursales s ON p.sucursal = s.id 
			JOIN marcas m ON m.id = p.marca 
            WHERE p.anulado IS NULL
            ORDER BY p.id DESC;");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Buscar($q)
	{
		try {

			$q = '%' . $q . '%';
			$stm = $this->pdo->prepare("SELECT *, (SELECT imagen FROM imagenes WHERE id_producto = p.id limit 1) as imagen FROM productos p WHERE producto LIKE ? ORDER BY id DESC");

			$stm->execute(array($q));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Obtener($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT *, (SELECT categoria FROM categorias c WHERE c.id= p.id_categoria) AS categoria FROM productos p WHERE p.id = ?");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerPorCodigo($codigo)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT 
					  				*, 
									(SELECT categoria FROM categorias c WHERE c.id= p.id_categoria) AS categoria 
								FROM productos p 
					  			WHERE p.codigo = ?");


			$stm->execute(array($codigo));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerDeTallerPorCodigo($codigo)
	{
		try {
			$pdo_taller = Database::StartUp_taller();

			$stm = $pdo_taller
				->prepare("SELECT 
					  				*, 
									(SELECT categoria FROM categorias c WHERE c.id= p.id_categoria) AS categoria 
								FROM productos p 
					  			WHERE p.codigo = ?");


			$stm->execute(array($codigo));

			$pdo_taller = null;
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerLimpio($id)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT * FROM productos p WHERE p.id = ?");


			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Codigo($codigo)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT *, (SELECT categoria FROM categorias c WHERE c.id= p.id_categoria) AS categoria FROM productos p WHERE p.codigo = ?");


			$stm->execute(array($codigo));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}


	public function Ultimo()
	{
		try {
			$stm = $this->pdo->prepare("SELECT MAX(id) as id FROM productos LIMIT 1");

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
				->prepare("UPDATE productos SET anulado = 1 WHERE id = ?");

			$stm->execute(array($id));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Restar($data)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");

			$stm->execute(array($data->cantidad, $data->id_producto));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerSucursal($codigo, $sucursal)
	{
		try {
			$stm = $this->pdo
				->prepare("SELECT *, (SELECT categoria FROM categorias c WHERE c.id= p.id_categoria) AS categoria FROM productos p WHERE p.codigo = ? AND sucursal = ?");


			$stm->execute(array($codigo, $sucursal));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function RestarId($id_producto, $cantidad)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");

			$stm->execute(array($cantidad, $id_producto));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function SumarId($id_producto, $cantidad)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE productos SET stock = stock + ? WHERE id = ?");

			$stm->execute(array($cantidad, $id_producto));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function SumarPorCodigo($data)
	{ //SUMAR STOCK SEGUN UN CODIGO DE PRODUCTO
		// var_dump($data);
		try {
			$stm = $this->pdo
				->prepare("UPDATE productos SET stock = stock + ?, precio_costo = ?, precio_minorista = ?, precio_mayorista = ? WHERE codigo = ?");

			$stm->execute(array($data->cantidad, $data->precio_costo, $data->precio_minorista, $data->precio_mayorista, $data->codigo));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function Insertar($data)
	{
		try {
			$sql = "INSERT INTO productos (codigo, id_categoria, producto, marca, descripcion, precio_costo, precio_minorista, precio_mayorista, stock, stock_minimo, descuento_max, importado, iva, sucursal, anulado) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->codigo,
						$data->id_categoria,
						$data->producto,
						$data->marca,
						$data->descripcion,
						$data->precio_costo,
						$data->precio_minorista,
						$data->precio_mayorista,
						$data->stock,
						$data->stock_minimo,
						$data->descuento_max,
						$data->importado,
						$data->iva,
						$data->sucursal,
						$data->anulado
					)
				);
			return "Agregado";
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Compra($data)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE productos SET stock = stock + ?, precio_costo = ?, precio_minorista = ?, precio_mayorista = ? WHERE id = ?");

			$stm->execute(array(
				$data->cantidad,
				$data->precio_compra,
				$data->precio_min,
				$data->precio_may,
				$data->id_producto
			));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Sumar($data)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE productos SET stock = stock + ? WHERE id = ?");

			$stm->execute(array($data->cantidad, $data->id_producto));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function SumarProducto($data)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE productos SET stock = stock + ? WHERE id = ?");

			$stm->execute(array($data->cantidad, $data->id_producto));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function SumarDevolucion($data)
	{
		try {

			$stm = $this->pdo
				->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");

			$stm->execute(array($data->cantidad, $data->id_producto));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function RestarDevolucion($data)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE productos SET stock = stock + ? WHERE id = ?");

			$stm->execute(array($data->cantidad, $data->id_producto));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function GuardarStock($data)
	{
		try {
			$sql = "UPDATE productos SET stock    = ? WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(

						$data->stock,
						$data->id
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try {
			$sql = "UPDATE productos SET 

						codigo              = ?,
						id_categoria        = ?,
						producto      		= ?,
						marca      		    = ?,
						descripcion         = ?,
						precio_costo        = ?, 
						precio_minorista    = ?,
						precio_mayorista    = ?,
						precio_promo        = ?,
						desde               = ?,
						hasta               = ?,
						stock               = ?,
						stock_minimo        = ?,
						descuento_max       = ?,
						importado           = ?,
						iva                 = ?,
						sucursal            = ?,
						tipo				= ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->codigo,
						$data->id_categoria,
						$data->producto,
						$data->marca,
						$data->descripcion,
						$data->precio_costo,
						$data->precio_minorista,
						$data->precio_mayorista,
						$data->precio_promo,
						$data->desde,
						$data->hasta,
						$data->stock,
						$data->stock_minimo,
						$data->descuento_max,
						$data->importado,
						$data->iva,
						$data->sucursal,
						$data->tipo,
						$data->id
					)
				);
			return "Modificado";
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function Registrar($data)
	{
		try {
			$sql = "INSERT INTO productos (codigo, id_categoria, producto, marca, descripcion, precio_costo, precio_minorista, precio_mayorista, precio_promo, desde, hasta, stock, stock_minimo, descuento_max, importado, iva, sucursal, anulado, tipo) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

			$this->pdo->prepare($sql)
				->execute(
					array(
						$data->codigo,
						$data->id_categoria,
						$data->producto,
						$data->marca,
						$data->descripcion,
						$data->precio_costo,
						$data->precio_minorista,
						$data->precio_mayorista,
						$data->precio_promo,
						$data->desde,
						$data->hasta,
						$data->stock,
						$data->stock_minimo,
						$data->descuento_max,
						$data->importado,
						$data->iva,
						$data->sucursal,
						$data->anulado,
						$data->tipo
					)
				);
			return "Agregado";
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
}
