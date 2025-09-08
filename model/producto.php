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
    public $ultimo_precio;
    public $precio_minorista;
    public $precio_mayorista;
    public $precio_intermedio;
    public $apartir;
    public $fardo;
    public $preciob;
    public $stock;
    public $stock_s1;
    public $stock_s2;
    public $stock_minimo;
    public $descuento_max;
    public $importado;
    public $iva;
    public $sucursal;
    public $anulado;
	public $sinfactura;
	public $confactura;


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

	public function Listar()
	{
		try
		{
			$result = array();
            if(!isset($_SESSION['nivel'])){
                session_start();
            }
            
            
			$stm = $this->pdo->prepare("SELECT *, p.id, s.sucursal 
			FROM productos p 
			      LEFT JOIN categorias c ON p.id_categoria = c.id
			      LEFT JOIN sucursales s ON p.sucursal = s.id 
			      LEFT JOIN marcas m ON m.id = p.marca  
			      WHERE p.anulado IS NULL 
			      ORDER BY CAST(p.codigo AS INT) ASC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarMarca($marca)
	{
		try
		{
			$result = array();
           
            if($marca!=''){
                $marca = " AND  p.marca = ".$marca;
            }else{
                $marca = "";
            }
            
            
			$stm = $this->pdo->prepare("SELECT *, p.id, s.sucursal 
			FROM productos p 
			      LEFT JOIN categorias c ON p.id_categoria = c.id
			      LEFT JOIN sucursales s ON p.sucursal = s.id 
			      LEFT JOIN marcas m ON m.id = p.marca  
			      WHERE p.anulado IS NULL $marca
			      ORDER BY CAST(p.codigo AS INT) ASC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarProd($id)
	{
		try
		{
			$result = array();
            if(!isset($_SESSION['nivel'])){
                session_start();
            }
            
			$stm = $this->pdo->prepare("SELECT p.id, s.sucursal, p.precio_costo, p.stock_s1
			FROM productos p 
			LEFT JOIN categorias c ON p.id_categoria = c.id
			LEFT JOIN sucursales s ON p.sucursal = s.id 
			LEFT JOIN marcas m ON m.id = p.marca  
			WHERE p.anulado IS NULL AND p.id = ?");
			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarProdctoFactura()
	{
		try
		{
			$result = array();
            if(!isset($_SESSION['nivel'])){
                session_start();
            }
            if(true){
                $sucursal = "WHERE p.sucursal = ".$_SESSION['sucursal'];
            }else{
                $sucursal = "";
            }
            
            
			$stm = $this->pdo->prepare("SELECT *, p.id, s.sucursal FROM productos p LEFT JOIN categorias c ON p.id_categoria = c.id
			LEFT JOIN sucursales s ON p.sucursal = s.id 
			JOIN marcas m ON m.id = p.marca  
			WHERE p.anulado IS NULL AND p.confactura>0 
			ORDER BY p.id ASC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function ListarAjax()
	{
		try
		{
            
			$stm = $this->pdo->prepare("SELECT *, p.id, s.sucursal, sub.categoria AS sub_categoria, sub.id_padre, m.marca 
			FROM productos p 
			LEFT JOIN categorias sub ON p.id_categoria = sub.id 
			LEFT JOIN categorias c ON sub.id_padre = c.id 
			LEFT JOIN sucursales s ON p.sucursal = s.id 
			LEFT JOIN marcas m ON m.id = p.marca 
			WHERE p.anulado IS NULL ORDER BY CAST(p.codigo AS INT) ASC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function consultarVenta($id_venta)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT SUM(monto) as total FROM ingresos WHERE id_venta = ?");
			$stm->execute(array($id_venta));
			return $stm->fetch(PDO::FETCH_OBJ);
			
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarVenta($id_venta)
	{
		try
		{
            
			$stm = $this->pdo->prepare("SELECT * FROM productos WHERE id IN (SELECT id_producto FROM ventas WHERE id_venta = ?) AND id NOT IN (SELECT id_producto FROM devoluciones_tmp) ORDER BY id DESC");
			$stm->execute(array($id_venta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarBuscar($q)
	{
		try
		{
			$result = array();
            if(!isset($_SESSION['nivel'])){
                session_start();
            }
            if($_SESSION['nivel']!=1 ){
                $sucursal = "WHERE p.sucursal = ".$_SESSION['sucursal'];
            }else{
                $sucursal = "";
            }
            
            if($q != ""){
                $sucursal = "AND p.sucursal = ".$q;
            }else{
                $sucursal = "";
            }
            
			$stm = $this->pdo->prepare("SELECT *, p.id, s.sucursal FROM productos p JOIN categorias c ON p.id_categoria = c.id LEFT JOIN sucursales s ON p.sucursal = s.id JOIN marcas m ON m.id = p.marca $sucursal  ORDER BY p.id DESC LIMIT 50");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarVentaProducto($id_venta)
	{
		try
		{
            
			$stm = $this->pdo->prepare("
			SELECT *,
			(SELECT dev.id_venta FROM devoluciones_ventas dev WHERE dev.id_venta = v.id_venta AND v.id_producto = dev.id_producto LIMIT 1) as devolucion,
			(SELECT SUM(dev.cantidad) FROM devoluciones_ventas dev WHERE dev.id_venta = v.id_venta AND v.id_producto = dev.id_producto LIMIT 1) as cantidad_devuelta,
			(v.cantidad - COALESCE((SELECT SUM(dev.cantidad) FROM devoluciones_ventas dev WHERE dev.id_venta = v.id_venta AND v.id_producto = dev.id_producto), 0)) as cantidad_restante
			FROM ventas v
			LEFT JOIN productos p ON v.id_producto = p.id
			WHERE id_venta = ?
			ORDER BY p.id");
			$stm->execute(array($id_venta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarTodo()
	{
		try
		{
			$result = array();
          
			$stm = $this->pdo->prepare("SELECT *, p.id, s.sucursal, p.sucursal as id_sucursal 
			FROM productos p 
			JOIN categorias c ON p.id_categoria = c.id 
			LEFT JOIN sucursales s ON p.sucursal = s.id 
			JOIN marcas m ON m.id = p.marca ORDER BY p.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarTodoBalance()
	{
		try
		{
			$result = array();
          
			$stm = $this->pdo->prepare("SELECT *, p.id, s.sucursal, p.sucursal as id_sucursal 
			FROM productos p 
			LEFT JOIN categorias c ON p.id_categoria = c.id 
			LEFT JOIN sucursales s ON p.sucursal = s.id 
			LEFT JOIN marcas m ON m.id = p.marca 
            WHERE p.anulado IS NULL
            ORDER BY p.id DESC;");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function Buscar($q)
	{
		try
		{
			 
			$q = '%'.$q.'%';
			$stm = $this->pdo->prepare("SELECT *, (SELECT imagen FROM imagenes WHERE id_producto = p.id limit 1) as imagen FROM productos p WHERE producto LIKE ? ORDER BY id DESC");

			$stm->execute(array($q));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}


	public function Obtener($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT *, (SELECT categoria FROM categorias c WHERE c.id= p.id_categoria) AS categoria FROM productos p WHERE p.id = ?");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function BuscarCodigo($codigo)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM productos p WHERE p.codigo = ?");
			          

			$stm->execute(array($codigo));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	public function ObtenerLimpio($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM productos p WHERE p.id = ?");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Codigo($codigo)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT *, (SELECT categoria FROM categorias c WHERE c.id= p.id_categoria) AS categoria FROM productos p WHERE p.codigo = ?");
			          

			$stm->execute(array($codigo));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}


	public function Ultimo()
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT MAX(id) as id FROM productos LIMIT 1");
			          

			$stm->execute();
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
			            ->prepare("UPDATE productos SET anulado = 1 WHERE id = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Restar($data)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE productos SET stock_s1 = stock_s1 - ?, stock = stock - ? WHERE id = ?");			          

			$stm->execute(array($data->cantidad, $data->cantidad, $data->id_producto));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function RestarStock($data)
	{
		try 
		{

		

			$stm = $this->pdo
			            ->prepare("UPDATE productos SET stock = stock - ?, stock_s1= stock_s1 - ? WHERE id = ?");			          

			$stm->execute(array($data->cantidad, $data->cantidad, $data->id_producto));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	public function RestarConFactura($data)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE productos SET confactura = confactura - ? WHERE id = ?");			          

			$stm->execute(array($data->can_factura, $data->prod_factura));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	public function RestarSinFactura($data)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE productos SET sinfactura = sinfactura - ? WHERE id = ?");			          

			$stm->execute(array($data->cantidad,  $data->id_producto));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function RestarFactura($data)
	{
		try {
			$stm = $this->pdo
				->prepare("UPDATE productos SET confactura = confactura - ? WHERE id = ?");

			$stm->execute(array($data->cantidad, $data->id_producto));
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	public function ObtenerSucursal($codigo, $sucursal)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT *, (SELECT categoria FROM categorias c WHERE c.id= p.id_categoria) AS categoria FROM productos p WHERE p.codigo = ? AND sucursal = ?");
			          

			$stm->execute(array($codigo, $sucursal));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	public function RestarId($id_producto, $cantidad)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE productos SET stock_s1 = stock_s1 - ?, stock = stock - ? WHERE id = ?");			          

			$stm->execute(array($cantidad, $cantidad, $id_producto));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function SumarId($id_producto, $cantidad)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE productos SET stock_s1 = stock_s1 + ?, stock = stock + ? WHERE id = ?");			          

			$stm->execute(array($cantidad, $cantidad,  $id_producto));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	public function Insertar($data)
	{
		try 
		{
		$sql = "INSERT INTO productos (codigo, id_categoria, producto, marca, descripcion, precio_costo, precio_minorista, precio_mayorista, stock_s1, stock_s2, stock_minimo, descuento_max, importado, iva, sucursal, anulado) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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
                    $data->stock_s1,
                    $data->stock_s2,
                    $data->stock_minimo,
                    $data->descuento_max,
                    $data->importado,
                    $data->iva,
                    $data->sucursal,
                    $data->anulado
                )
			);
		return "Agregado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Compra($data)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE productos SET precio_minorista = ?, precio_mayorista = ?, precio_intermedio = ? WHERE id = ?");			          

			$stm->execute(array(
			
				$data->precio_min,
				$data->precio_may,
				$data->precio_intermedio,
				$data->id_producto));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Sumar($data)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE productos SET stock_s1 = stock_s1 + ?, stock = stock + ? WHERE id = ?");			          

			$stm->execute(array($data->cantidad,$data->cantidad, $data->id_producto));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function SumarStock($data)
	{
		try 
		{
			

			$stm = $this->pdo
			            ->prepare("UPDATE productos SET stock_s1 = stock_s1 + ?, stock = stock + ? WHERE id = ?");			          

			$stm->execute(array($data->cantidad,$data->cantidad, $data->id_producto));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function SumarSinfactura($data)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE productos SET sinfactura = sinfactura + ? WHERE id = ?");			          

			$stm->execute(array($data->cantidad, $data->id_producto));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	public function SumarConfactura($data)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE productos SET confactura = confactura + ? WHERE id = ?");			          

			$stm->execute(array($data->can_factura, $data->prod_factura));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	public function SumarProducto($data)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE productos SET stock = stock + ?, stock_s1 = stock_s1 + ? WHERE id = ?");			          

			$stm->execute(array($data->cantidad, $data->cantidad, $data->id_producto));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	public function SumarDevolucion($data)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE productos SET stock_s1 = stock_s1 + ?, stock = stock + ? WHERE id = ?");			          

			$stm->execute(array($data->cantidad, $data->cantidad, $data->id_producto));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function RestarDevolucion($data)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE productos SET stock_s1 = stock_s1 - ?, stock = stock - ? WHERE id = ?");			          

			$stm->execute(array($data->cantidad,$data->cantidad, $data->id_producto));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	public function GuardarStock($data)
	{
		try 
		{
			$sql = "UPDATE productos SET stock_s1  = ?, stock = ? WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				        $data->stock,
                        $data->stock,
                        $data->id
					)
				);
	
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try 
		{
			$sql = "UPDATE productos SET 

						codigo              = ?,
						id_categoria        = ?,
						producto      		= ?,
						marca      		    = ?,
						descripcion         = ?,
						precio_costo        = ?,
						ultimo_precio        = ?,
						precio_minorista    = ?,
						precio_mayorista    = ?,
						precio_intermedio   = ?,
						apartir             = ?,
						fardo               = ?,
						preciob             = ?,
						precio_promo        = ?,
						desde               = ?,
						hasta               = ?,
						stock_s1            = ?,
						stock_s2            = ?,
						stock               = ?,
						stock_minimo        = ?,
						descuento_max       = ?,
						importado           = ?,
						iva                 = ?,
						sucursal            = ?,
						sinfactura          = ?,
						confactura          = ?
						
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
                        $data->ultimo_precio,
                        $data->precio_minorista,
                        $data->precio_mayorista,
                        $data->precio_intermedio,
                        $data->apartir,
                        $data->fardo,
                        $data->preciob,
                        $data->precio_promo,
                        $data->desde,
                        $data->hasta,
                        $data->stock_s1,
                        $data->stock_s2,
                        $data->stock_s1,
                        $data->stock_minimo,
                        $data->descuento_max,
                        $data->importado,
                        $data->iva,
                        $data->sucursal,
						$data->sinfactura,
						$data->confactura,
                        $data->id
					)
				);
		return "Modificado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	public function ActualizarCosto($cantidad, $id_producto, $new_precio)
	{
		try 
		{
			$sql = "UPDATE productos SET 

					
						stock_s1    = stock_s1 + ?,
						stock       = stock + ?,
						precio_costo=?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	
                        $cantidad,
                        $cantidad,
                        $new_precio,
                        $id_producto
					)
				);
		return "Modificado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ActualizarPrecio($cantidad, $id_producto)

	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE productos SET stock_s1 = stock_s1 + ?, stock = stock + ?  WHERE id = ?");			          

			$stm->execute(array($cantidad, $cantidad , $id_producto));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar(producto $data)
	{
		try 
		{
		$sql = "INSERT INTO productos (codigo, id_categoria, producto, marca, descripcion, precio_costo, ultimo_precio, precio_minorista, precio_mayorista, precio_intermedio, apartir, fardo, preciob, precio_promo, desde, hasta, stock_s1, stock_s2,stock, stock_minimo, descuento_max, importado, iva, sucursal, anulado, sinfactura, confactura) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->codigo,
					$data->id_categoria,
				    $data->producto,
				    $data->marca, 
                    $data->descripcion,
                    $data->precio_costo, 
                    $data->ultimo_precio,
                    $data->precio_minorista,
                    $data->precio_mayorista,
					$data->precio_intermedio,
                    $data->apartir,
                    $data->fardo,
                    $data->preciob,
                    $data->precio_promo,
                    $data->desde,
                    $data->hasta,
                    $data->stock_s1,
                    $data->stock_s2,
                    $data->stock_s1,
                    $data->stock_minimo,
                    $data->descuento_max,
                    $data->importado,
                    $data->iva,
                    $data->sucursal,
                    $data->anulado,
					$data->sinfactura,
					$data->confactura
                )
			);
		return "Agregado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}