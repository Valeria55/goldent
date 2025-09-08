<?php
class presupuesto_compra
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

	public function Listar($id_presupuesto)
	{
		try
		{
			
			if($id_presupuesto==0){
				$stm = $this->pdo->prepare("SELECT v.id, v.id_presupuesto, v.comprobante, v.precio_min, v.precio_may, v.precio_intermedio, v.metodo, v.anulado, v.moneda, contado, p.producto, SUM(subtotal) as subtotal, descuento, SUM(total) as total, AVG(margen_ganancia) as margen_ganancia, fecha_compra, nro_comprobante, c.nombre as nombre_cli, v.id_producto, (SELECT user FROM usuario WHERE id = v.id_vendedor) as vendedor FROM presupuesto_compras v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id GROUP BY v.id_presupuesto DESC");
				$stm->execute();
			}else{
				$stm = $this->pdo->prepare("SELECT v.id, p.producto,v.comprobante, v.precio_min, v.precio_may, v.precio_intermedio, v.metodo, v.anulado, contado, p.codigo, p.stock_s1, v.cantidad, v.precio_compra, subtotal, descuento, total, margen_ganancia, v.moneda, fecha_compra, nro_comprobante, c.nombre as nombre_cli, v.id_producto FROM presupuesto_compras v LEFT JOIN productos p ON v.id_producto = p.id LEFT JOIN clientes c ON v.id_cliente = c.id WHERE v.id_presupuesto = ? AND v.anulado = 0");
				$stm->execute(array($id_presupuesto));
			}

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	// Para listar en la vista de presupuestos de compra
	public function Listarr()
	{
		try
		{ session_start();
			$userId= $_SESSION['user_id'];
			if(($_SESSION['nivel']<3)){
				$vendedor = "";
            }else{
				$vendedor = "AND v.id_vendedor = '$userId'";
                
            }
			
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, SUM((v.cantidad*v.precio_compra)-(v.descuento)) AS total
			FROM presupuesto_compras v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE 1=1 $vendedor AND v.anulado=0 GROUP BY v.id_presupuesto ORDER BY v.id DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarFiltros($desde, $hasta)
	{
		try
		{ session_start();
			$rango = ($desde==0)? "":"AND fecha_compra BETWEEN '$desde' AND '$hasta'";
			$userId= $_SESSION['user_id'];
			if(($_SESSION['nivel']<3)){
				$vendedor = "";
            }else{
				$vendedor = "AND v.id_vendedor = '$userId'";
                
            }
			
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, SUM((v.cantidad*v.precio_compra)-(v.descuento)) AS total
			FROM presupuesto_compras v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE 1=1 $vendedor AND v.anulado=0 $rango GROUP BY v.id_presupuesto ORDER BY v.id DESC");
			$stm->execute(array());

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
			          ->prepare("SELECT * FROM presupuesto_compras WHERE id_presupuesto = ? LIMIT 1");
			          

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
			          ->prepare("SELECT * FROM presupuesto_compras WHERE id = ? LIMIT 1");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ObtenerUno($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM presupuesto_compras WHERE id_presupuesto = ? LIMIT 1");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ObtenerPorProductoUsuario($data)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM presupuesto_compras WHERE id_producto = ? AND id_vendedor = ? AND anulado=0");
			          

			$stm->execute(array($data->id_producto, $data->id_vendedor));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ObtenerId_presupuesto($id)
	{
		try 
		{
			$stm = $this->pdo->prepare("SELECT * FROM presupuesto_compras WHERE id_presupuesto = ?");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ListarDetalle($id_presupuesto)
	{
		try
		{
			
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, v.id as id, p.codigo, v.precio_intermedio as precio_inter
			FROM presupuesto_compras v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE id_presupuesto = ? AND v.anulado = 0 ORDER BY v.id ASC");
			$stm->execute(array($id_presupuesto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function CambiarEstado($data)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE presupuesto_compras SET estado='Comprado' WHERE id_presupuesto = ?");
			$stm->execute(array($data->id_presupuesto));			          

		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function VolverEstado($id_presupuesto)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE presupuesto_compras 
						SET estado = NULL
						WHERE id_presupuesto = ?");
			$stm->execute(array($id_presupuesto));			          

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
			          ->prepare("SELECT MAX(id_presupuesto) as id_presupuesto FROM presupuesto_compras");
			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}


	public function Cantidad($id_item, $id_presupuesto, $cantidad)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("UPDATE presupuesto_compras SET cantidad = ?, subtotal = precio_compra * ?, total = precio_compra * ? WHERE id = ?");
			$stm->execute(array($cantidad, $cantidad, $cantidad, $id_item));
			$stm = $this->pdo
			          ->prepare("SELECT *, (SELECT SUM(total) FROM presupuesto_compras WHERE id_presupuesto = ? GROUP BY id_presupuesto) as total_presupuesto FROM presupuesto_compras WHERE id = ?");
			$stm->execute(array($id_presupuesto, $id_item));
		
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function PrecioCosto($id_item, $id_presupuesto, $precio_compra)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("UPDATE presupuesto_compras SET precio_compra = ?, subtotal = ? * cantidad, total = ? * cantidad WHERE id = ?");
			$stm->execute(array($precio_compra, $precio_compra, $precio_compra, $id_item));
			$stm = $this->pdo
			          ->prepare("SELECT *, (SELECT SUM(total) FROM presupuesto_compras WHERE id_presupuesto = ? GROUP BY id_presupuesto) as total_presupuesto FROM presupuesto_compras WHERE id = ?");
			$stm->execute(array($id_presupuesto, $id_item));
		
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
			            ->prepare("UPDATE presupuesto_compras SET anulado = 1 WHERE id = ?");			          

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
			            ->prepare("DELETE FROM presupuesto_compras WHERE id_compra = ?");			          

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
			            ->prepare("UPDATE presupuesto_compras SET anulado = 1 WHERE id_presupuesto = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

/*	public function ActualizarPrecio($id_compra, $id_producto, $pre_promed)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE presupuesto_compras SET precio_compra = ? WHERE id_compra = ? AND id_producto =?");			          

			$stm->execute(array($pre_promed, $id_compra, $id_producto ));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}


*/
	public function Registrar($data)
	{
		try 
		{

		$sql = "INSERT INTO presupuesto_compras (id_presupuesto, id_cliente, id_vendedor, id_producto, precio_compra, precio_min, precio_may, precio_intermedio, subtotal, descuento, cantidad, total, comprobante, nro_comprobante, fecha_compra ) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->id_presupuesto,
                    $data->id_cliente,
                    $data->id_vendedor,
                    $data->id_producto,           
                    $data->precio_compra,
                    $data->precio_min,
                    $data->precio_may,
					$data->precio_intermedio,
                    $data->subtotal,
                    $data->descuento,
					$data->cantidad,
                    $data->total,
                    $data->comprobante,
                    $data->nro_comprobante,
                    $data->fecha_compra
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
			$sql = "UPDATE presupuesto_compras SET
						id_presupuesto     = ?,
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
                        $data->id_presupuesto,
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
}