<?php
class inventario
{
	private $pdo;
    
    public $id;
    public $id_inventario;
    public $id_producto;
    public $id_usuario;
    public $stock_actual;
    public $stock_real;
    public $faltante;
    public $fecha;
    public $fecha_stock_real;

	public function __CONSTRUCT()
	{
		try
		{
			$this->pdo = Database::StartUp();     
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	//Listar inventario
	public function Inventario($id_c)
	{
		try
		{
			
			$result = array();

			$stm = $this->pdo->prepare("SELECT 
			cat.categoria AS categoria_hijo,
            (SELECT categoria FROM categorias WHERE categorias.id = cat.id_padre) AS categoria,
			i.stock_actual, i.id AS id_i, p.codigo AS codigo, p.producto AS producto, u.user AS usuario, p.precio_costo AS costo, p.precio_minorista AS venta, (SELECT marca FROM marcas WHERE id = p.marca) AS marca,
			IF(i.stock_real IS NULL, 0, i.stock_real) AS inventario,
			IF(i.faltante IS NULL,i.stock_actual,i.faltante) AS faltante,
			IF(i.faltante IS NULL, p.precio_minorista * i.stock_actual,p.precio_minorista * i.faltante ) AS monto
				FROM inventario i
				LEFT JOIN productos p ON i.id_producto=p.id
				LEFT JOIN usuario u ON i.id_usuario = u.id
				LEFT JOIN categorias cat ON p.id_categoria = cat.id
				WHERE i.id_inventario = ?
				ORDER BY p.id_categoria DESC");
			$stm->execute(array($id_c));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
//Lista la fecha del dia
	public function Listar($fecha)
	{
		try
		{
			
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, i.id AS id_i, p.codigo AS codigo, p.producto AS producto, u.user AS usuario 
				FROM inventario i
				LEFT JOIN productos p ON i.id_producto=p.id
				LEFT JOIN usuario u ON i.id_usuario = u.id
				WHERE i.fecha = ?
				ORDER BY id_i DESC");
			//le da como parametro fecha
			$stm->execute(array($fecha));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
 	public function ListarProducto($id_producto, $desde, $hasta)
	{
		try
		{
			
			$rango = ($desde==0)? "":"AND fecha_stock_real >= '$desde' AND fecha_stock_real <= '$hasta'";
			$stm = $this->pdo->prepare("SELECT v.id, 
			p.producto,
			v.anulado,  
			p.codigo,
			v.fecha,
			(v.stock_real) AS cantidad,  
			v.fecha_stock_real,
			v.id_producto,
			(SELECT u.user FROM usuario u WHERE u.id = v.id_usuario) as vendedor
			FROM inventario v  
			LEFT JOIN productos p ON v.id_producto = p.id 
		    WHERE v.id_producto = ? $rango AND v.anulado = 0 GROUP BY v.id_inventario");
			$stm->execute(array($id_producto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ObtenerSumatorias($id_c, $q)
	{
		try {

			switch ($q) {
				case 1: // productos sin cargar
					$filtro = 'AND i.stock_real IS NULL';
					break;

				case 2: // productos cargados
					$filtro = 'AND i.stock_real IS NOT NULL';
					break;

				default:
					$filtro = '';
					break;
			}

			$sql = "SELECT sum( (IF(i.faltante IS NULL, 0, i.faltante)) * p.precio_minorista) AS monto_faltante
						FROM inventario i
						LEFT JOIN productos p ON i.id_producto=p.id	
						WHERE i.id_inventario = '$id_c'
						$filtro";

			$stm = $this->pdo->prepare($sql);

			$stm->execute();

			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
//Lista la fecha del dia

	public function ListarSS($GET, $table, $primaryKey, $columns)
	{
		// if ($_REQUEST['fecha'] == '') {
		// 	$fecha = date('Y-m-d');
		// } else {
		// 	$fecha = $_REQUEST['fecha'];
		// }
		$id_c = $_REQUEST['id_c'];
		
		$q = $_REQUEST['q'];
		$especiales   = array("'", '"', ";"
		);
		$id_c = str_replace($especiales, "", $id_c);
		$q = str_replace($especiales, "", $q);

		switch ($q) {
			case 1: // productos sin cargar
				$filtro = 'AND i.stock_real IS NULL ORDER BY id_i DESC';
				break;
			
			case 2: // productos cargados
				$filtro = 'AND i.stock_real IS NOT NULL ORDER BY i.fecha_stock_real DESC';
				break;
			
			default:
				$filtro = 'ORDER BY id_i DESC';
				break;
		}

		$sql = "SELECT i.*, 
						i.id AS id_i, 
						p.codigo AS codigo, 
						i.id_producto AS rownum,
						p.producto AS producto, 
						(SELECT marca FROM marcas WHERE id = p.marca) AS marca,
						cat.categoria,
						u.user AS usuario , 
						p.precio_minorista,
						p.precio_costo,
						c_i.fecha_cierre
				FROM inventario i
				LEFT JOIN cierre_inventario c_i ON i.id_inventario=c_i.id	
				LEFT JOIN productos p ON i.id_producto=p.id	
				LEFT JOIN categorias cat ON p.id_categoria = cat.id	
				LEFT JOIN usuario u ON i.id_usuario = u.id
				WHERE i.id_inventario = '$id_c'
				$filtro
				";

		$table = <<<EOT
		 (
		    $sql
		 ) temp
EOT;

		require('model/ssp.class.php');
		$sql_details = array('user' => USER, 'pass' => PASS, 'db'   => DB, 'host' => HOST);
		return SSP::simple($GET, $sql_details, $table, $primaryKey, $columns);
	}

	public function ListarInventario($id_c)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT i.*, i.id AS id_i, p.codigo AS codigo, ROW_NUMBER() OVER (ORDER BY id_i ASC) AS rownum,
						p.producto AS producto, 
						cat.categoria AS categoria_hijo,
                        (SELECT categoria FROM categorias WHERE categorias.id = cat.id_padre) AS categoria,
						u.user AS usuario , 
						p.precio_minorista,
						p.precio_costo,
						c_i.fecha_cierre
				FROM inventario i
				LEFT JOIN cierre_inventario c_i ON i.id_inventario=c_i.id	
				LEFT JOIN productos p ON i.id_producto=p.id	
				LEFT JOIN categorias cat ON p.id_categoria = cat.id	
				LEFT JOIN usuario u ON i.id_usuario = u.id
				WHERE i.id_inventario = ?
				ORDER BY categoria ASC ");
			//le da como parametro fecha
			$stm->execute(array($id_c));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function StockDeInventario($data)
	{ // setear nuevo stock en productos
		try {
			$sql = "UPDATE productos p, inventario i 
					SET p.stock_s1 = 
							CASE 
								WHEN i.stock_real IS NULL 
									THEN (0) 
								ELSE 
									i.stock_real - (SELECT 
														IF((SUM(v.cantidad) IS NULL), 0 ,SUM(v.cantidad)) AS cantidad
													FROM ventas v 
													WHERE fecha_venta >= (i.fecha_stock_real)  
													AND v.anulado = 0
													AND v.id_producto = i.id_producto) -- restando ventas
													+ (SELECT 
														IF((SUM(v.cantidad) IS NULL), 0 ,SUM(v.cantidad)) AS cantidad
													FROM compras v 
													WHERE fecha_compra >= (i.fecha_stock_real)  
													AND v.anulado = 0
													AND v.id_producto = i.id_producto) -- sumando compras
							END
					WHERE i.id_inventario = ? AND i.id_producto = p.id;
					";

			$this->pdo->prepare($sql)
				->execute(
					array(

						$data->id
					)
				);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
		public function ListarInventarioNewSobrante($id_c)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT i.*,p.precio_costo,p.precio_minorista, c.categoria, p.codigo, p.producto, COALESCE(SUM(v.cantidad), 0) as cantidad_total_vendida 
			FROM inventario i 
			LEFT JOIN productos p ON i.id_producto = p.id 
			LEFT JOIN categorias c ON p.id_categoria = c.id 
			LEFT JOIN cierre_inventario ci ON ci.id = i.id_inventario
			LEFT JOIN ventas v ON i.id_producto = v.id_producto AND v.fecha_venta >= ci.fecha_apertura AND v.fecha_venta <= i.fecha_stock_real AND v.anulado=0
			WHERE i.id_inventario = ? AND i.faltante < 0 
			GROUP BY i.id_producto
			ORDER BY c.categoria ASC; ");
			//le da como parametro fecha
			$stm->execute(array($id_c));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarInventarioNew($id_c)
	{
		try {
			$result = array();

			$stm = $this->pdo->prepare("SELECT i.*,p.precio_costo,p.precio_minorista, c.categoria, p.codigo, p.producto, COALESCE(SUM(v.cantidad), 0) as cantidad_total_vendida 
			FROM inventario i 
			LEFT JOIN productos p ON i.id_producto = p.id 
			LEFT JOIN categorias c ON p.id_categoria = c.id 
			LEFT JOIN cierre_inventario ci ON ci.id = i.id_inventario
			LEFT JOIN ventas v ON i.id_producto = v.id_producto AND v.fecha_venta >= ci.fecha_apertura AND v.fecha_venta <= i.fecha_stock_real AND v.anulado=0
			WHERE i.id_inventario = ? AND i.faltante > 0 
			GROUP BY i.id_producto
			ORDER BY c.categoria ASC; ");
			//le da como parametro fecha
			$stm->execute(array($id_c));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}
	public function ListarRango($desde)
	{
		try
		{
			

			$result = array();

			$stm = $this->pdo->prepare("SELECT *, i.id AS id_i, p.codigo AS codigo, p.producto AS producto, u.user AS usuario 
				FROM inventario i
				LEFT JOIN productos p ON i.id_producto=p.id
				LEFT JOIN usuario u ON i.id_usuario = u.id
				WHERE i.fecha = ?
				ORDER BY id_i DESC");
			//le da como parametro fecha
			$stm->execute(array($desde));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}


	public function StockReal($data)
	{
		try 
		{
			$sql = "UPDATE inventario  
						SET 	stock_real = ?, 
								faltante = ?, 
								fecha_stock_real = ?  
						WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
                        $data->stock_real,
                        $data->faltante,
                        $data->fecha_stock_real,
                        $data->id
					)
				);
			return json_encode($this->ObtenerConProducto($data->id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}


	public function ObtenerConProducto($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT 
					  				i.*, 
									(SELECT producto 
										FROM productos 
										WHERE id = i.id_producto) AS producto 
										
								FROM inventario i WHERE id = ?");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	public function Obtener($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM inventario WHERE id = ?");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Eliminar($id)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("DELETE FROM inventario WHERE id = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try 
		{
			$sql = "UPDATE inventario SET 

						inventario      		= ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$data->id_producto,
				    	$data->id_usuario,
				    	$data->stock_actual,
				    	$data->stock_real,
				    	$data->faltante,
				    	$data->id
					)
				);
		return "Modificado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar(inventario $data)
	{
		try 
		{
		$sql = "INSERT INTO inventario (id_inventario, id_producto, id_usuario, stock_actual, stock_real, faltante, fecha) 
		        VALUES (?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
						$data->id_inventario,
						$data->id_producto,
				    	$data->id_usuario,
				    	$data->stock_actual,
				    	$data->stock_real,
				    	$data->faltante,
				    	$data->fecha
                )
			);
		return "Agregado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	public function RegistrarInventario(inventario $data)
	{
		try 
		{
		$sql = "INSERT INTO inventario 
					(id_inventario, id_producto, 
					id_usuario, stock_actual, 
					stock_real, faltante, fecha) 
						SELECT ?, p.id, ?, p.stock_s1, null, null, ? 
							FROM productos p 
							WHERE 
								p.anulado IS NULL
								-- AND p.activo = 'SI' 
								;";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
						$data->id_inventario,
				    	$data->id_usuario,
				    	$data->fecha
                )
			);
		// return "Agregado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}