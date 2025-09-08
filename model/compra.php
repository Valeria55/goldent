<?php
class compra
{
	private $pdo;
    
    public $id;
    public $id_compra;
    public $id_cliente;
    public $id_vendedor;
    public $id_producto;
    public $precio_compra;
    public $precio_min;
    public $precio_may;
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
	public $facturable;
    
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

	public function Listar($id_compra)
	{
		try
		{
			
			if($id_compra==0){
				//$id_vendedor = $_SESSION['user_id'];
				//$admin = ($_SESSION['nivel']==1)? "":" WHERE v.id_vendedor = $id_vendedor";
				$stm = $this->pdo->prepare("SELECT v.id, v.id_compra, v.comprobante, v.precio_min, v.precio_may, v.metodo, v.anulado, v.moneda, contado, p.producto, p.precio_intermedio, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_compra, nro_comprobante, c.nombre as nombre_cli, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor FROM compras v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id GROUP BY v.id_compra DESC");
				$stm->execute();
			}else{
				$stm = $this->pdo->prepare("SELECT v.id, p.producto,v.comprobante, v.id_presupuesto, v.precio_min, v.precio_may, v.metodo, v.anulado, contado, p.codigo, v.cantidad, v.precio_compra, subtotal, descuento, total, p.precio_intermedio, margen_ganancia, v.moneda, fecha_compra, nro_comprobante, c.nombre as nombre_cli, v.id_producto FROM compras v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id WHERE v.id_compra = ?");
				$stm->execute(array($id_compra));
			}

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarFiltro($id_compra, $desde, $hasta, $id_proveedor)
{
    try
    {
      
        $result = array();
        if (!empty($desde) && !empty($hasta)) {
            $rango = "AND CAST(fecha_compra as date) >= '$desde' AND CAST(fecha_compra as date) <= '$hasta'";
        } else {
            $rango = "";
		}
		$id_proveedor = $id_proveedor==0 ? "":"AND v.id_cliente = '$id_proveedor'";

		if($id_compra==0){
			//$id_vendedor = $_SESSION['user_id'];
			//$admin = ($_SESSION['nivel']==1)? "":" WHERE v.id_vendedor = $id_vendedor";
			$stm = $this->pdo->prepare("SELECT v.id, v.id_compra, v.comprobante, v.precio_min, v.precio_may, v.metodo, v.anulado, v.moneda, contado, p.producto, SUM(subtotal) 
			as subtotal, descuento, SUM(total) 
			as total, AVG(margen_ganancia) 
			as margen_ganancia, fecha_compra, nro_comprobante, c.nombre 
			as nombre_cli, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor 
			FROM compras v 
			LEFT JOIN productos p 
			ON v.id_producto = p.id 
			LEFT JOIN clientes c ON v.id_cliente = c.id 
			WHERE v.id IS NOT NULL $rango $id_proveedor
			GROUP BY v.id_compra DESC");
			$stm->execute();
		}else{
			$stm = $this->pdo->prepare("SELECT v.id, p.producto,v.comprobante, v.id_presupuesto, v.precio_min, v.precio_may, v.metodo, v.anulado, contado, p.codigo, v.cantidad, v.precio_compra, subtotal, descuento, total, margen_ganancia, v.moneda, fecha_compra, nro_comprobante, c.nombre as nombre_cli, v.id_producto FROM compras v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id WHERE v.id_compra = ?");
			$stm->execute(array($id_compra));
		}


			return $stm->fetchAll(PDO::FETCH_OBJ);
    }
		catch(Exception $e)
    {
        die($e->getMessage());
    }
	}
	public function ListarDia($fecha)
	{
		try
		{
			
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_compra, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_compra, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor FROM compras v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE CAST(v.fecha_compra AS date) = ?  GROUP BY v.id_compra DESC");
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
			
			$rango = ($desde==0)? "":"AND fecha_compra >= '$desde' AND fecha_compra <= '$hasta'";
			$stm = $this->pdo->prepare("SELECT v.id_compra,v.id, p.producto,v.comprobante, v.metodo, v.anulado, contado, p.codigo,p.iva, v.cantidad, (v.precio_compra/v.cambio) AS precio_compra, (v.subtotal/v.cambio) AS subtotal, descuento, (total/v.cambio) AS total, margen_ganancia, fecha_compra, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, (SELECT user FROM usuario u WHERE u.id = v.id_vendedor) as vendedor FROM compras v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id WHERE v.id_producto = ? $rango");
			$stm->execute(array($id_producto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarProductoCat($id_categoria, $desde, $hasta)
	{
		try
		{
			
			$rango = ($desde==0)? "":"AND fecha_compra >= '$desde' AND fecha_compra <= '$hasta'";
			$stm = $this->pdo->prepare("SELECT cat.categoria AS categoria, sub.categoria AS sub_categoria, v.id, p.producto,v.comprobante, v.metodo, v.anulado, contado, p.codigo,p.iva, v.cantidad, v.precio_compra, subtotal, descuento, total, margen_ganancia, fecha_compra, nro_comprobante, c.nombre as nombre_cli, c.ruc, c.direccion, c.telefono, v.id_producto, (SELECT user FROM usuario u WHERE u.id = v.id_vendedor) as vendedor FROM compras v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id LEFT JOIN categorias cat ON cat.id = sub.id_padre LEFT JOIN categorias sub ON sub.id = p.id_categoria WHERE (cat.id = ? OR sub.id = ?) $rango");
			$stm->execute(array($id_categoria, $id_categoria));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function AgrupadoProducto($desde, $hasta)
	{
		try
		{
			
			$stm = $this->pdo->prepare("SELECT v.id_compra, v.fecha_compra, p.producto, SUM(v.cantidad) as cantidad, SUM(v.total/v.cambio) as total, p.precio_costo, p.codigo FROM compras v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id WHERE CAST(v.fecha_compra AS date) >= ? AND CAST(v.fecha_compra AS date) <= ? AND v.anulado = 0 GROUP BY v.id_producto ORDER BY v.id_compra DESC");
			$stm->execute(array($desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarDiaSinAnular($fecha)
	{
		try
		{
			
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_compra, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_compra, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor FROM compras v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE CAST(v.fecha_compra AS date) = ? AND v.anulado <> 1  GROUP BY v.id_compra DESC");
			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarDiaItems($fecha)
	{
		try
		{
			
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_compra, a.nombre as nombre_cli, v.anulado, c.producto, v.descuento, v.precio_compra, v.cantidad, fecha_compra, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor FROM compras v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE CAST(v.fecha_compra AS date) = ? AND v.anulado <> 1 ORDER BY v.id ASC");
			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarMesItems($fecha)
	{
		try
		{
			
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_compra, a.nombre as nombre_cli, v.anulado, c.producto, v.descuento, v.precio_compra, v.cantidad, fecha_compra, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor FROM compras v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE MONTH(v.fecha_compra) = MONTH(?) AND YEAR(v.fecha_compra) = YEAR(?) AND v.anulado <> 1 ORDER BY v.id ASC");
			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarMesSinAnular($fecha)
	{
		try
		{
			
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_compra, a.nombre as nombre_cli, v.anulado, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_compra, nro_comprobante, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor FROM compras v LEFT JOIN productos c ON v.id_producto = c.id LEFT JOIN clientes a ON v.id_cliente = a.id WHERE MONTH(v.fecha_compra) = MONTH(?) AND YEAR(v.fecha_compra) = YEAR(?) AND v.anulado <> 1  GROUP BY v.id_compra DESC");
			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarDiaContado($fecha)
	{
		try
		{
			
			$stm = $this->pdo->prepare("SELECT v.metodo, v.contado, v.id_compra, cli.Nombre as nombre_cli, c.producto, SUM(subtotal) as subtotal, v.descuento, SUM(v.total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_compra, nro_comprobante FROM compras v LEFT JOIN productos c ON v.id_producto = c. LEFT JOIN clientes cli ON v.id_cliente = cli.id WHERE CAST(v.fecha_compra AS date) = ? AND contado = 'contado' GROUP BY v.id_compra DESC");
			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarMes($fecha)
	{
		try
		{
			
			$stm = $this->pdo->prepare("SELECT v.id_compra, cli.Nombre AS nombre_cli, v.metodo, c.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as ganancia, fecha_compra, nro_comprobante FROM compras v LEFT JOIN productos c ON v.id_producto = c. LEFT JOIN clientes cli ON cli.id = v.id_cliente  WHERE MONTH(v.fecha_compra) = MONTH(?) AND YEAR(v.fecha_compra) = YEAR(?) GROUP BY v.id_compra DESC");
			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function Detalles($id_compra)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT c.producto, subtotal, descuento, total, margen_ganancia, fecha_compra, nro_comprobante FROM compras v JOIN productos c ON v.id_producto = c.id WHERE v.id_compra = ?");
			$stm->execute(array($id_compra));

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
			          ->prepare("SELECT * FROM compras WHERE id_compra = ? LIMIT 1");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	public function ObtenerItem($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM compras WHERE id = ? LIMIT 1");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ObtenerUNO($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM compras WHERE id_compra = ? LIMIT 1");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Recibo($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM compras WHERE id_compra = ?");
			          

			$stm->execute(array($id));
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
			          ->prepare("SELECT MAX(id_compra) as id_compra FROM compras");
			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}


	public function Cantidad($id_item, $id_compra, $cantidad)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("UPDATE compras SET cantidad = ?, subtotal = precio_compra * ?, total = precio_compra * ? WHERE id = ?");
			$stm->execute(array($cantidad, $cantidad, $cantidad, $id_item));
			$stm = $this->pdo
			          ->prepare("SELECT *, (SELECT SUM(total) FROM compras WHERE id_compra = ? GROUP BY id_compra) as total_compra FROM compras WHERE id = ?");
			$stm->execute(array($id_compra, $id_item));
		
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function CancelarItem($id_item)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("DELETE FROM compras WHERE id = ?");			          

			$stm->execute(array($id_item));
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
			            ->prepare("DELETE FROM compras WHERE id_compra = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}


	public function Anular($id)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE compras SET anulado = 1 WHERE id_compra = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ActualizarPrecio($id_compra, $id_producto, $pre_promed)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE compras SET precio_compra = ? WHERE id_compra = ? AND id_producto =?");			          

			$stm->execute(array($pre_promed, $id_compra, $id_producto ));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try 
		{
			$sql = "UPDATE compras SET
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
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar($data)
	{
		try 
		{



		$sql = "INSERT INTO compras (id_compra, id_cliente, id_vendedor, id_producto, precio_compra, precio_min, precio_may, subtotal, descuento, iva, total, comprobante, nro_comprobante, cantidad, margen_ganancia, fecha_compra, metodo, moneda, cambio, banco, contado, facturable, id_presupuesto, cajas) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->id_compra,
                    $data->id_cliente,
                    $data->id_vendedor,
                    $data->id_producto,           
                    $data->precio_compra,
                    $data->precio_min,
                    $data->precio_may,
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
                    $data->moneda,
                    $data->cambio,
                    $data->banco,
                    $data->contado,
					$data->facturable,
					$data->id_presupuesto,
					$data->cajas
                   
                )
			);

		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}