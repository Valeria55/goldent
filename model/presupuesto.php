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
		{ session_start();
			$userId= $_SESSION['user_id'];
			if(($_SESSION['nivel']<3)){
				$vendedor = "";
            }else{
				$vendedor = "AND v.id_vendedor = '$userId'";
                
            }
			//$rango = $desde != '' ? "AND v.fecha_presupuesto = '$desde'":"";
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, v.anulado AS anulado,  SUM((v.cantidad*v.precio_venta)-(v.cantidad*v.descuento))AS total
			FROM presupuestos v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE 1=1 $vendedor AND v.anulado=0  GROUP BY v.id_presupuesto ORDER BY v.id DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function ListarPresupuestoDesde($desde)
	{
		try
		{ session_start();
			$userId= $_SESSION['user_id'];
			 if ($_SESSION['nivel']==1 || $_SESSION['nivel']==2 || $_SESSION['nivel']==4 ) { 
				$vendedor = "";
            }else{
				$vendedor = "AND v.id_vendedor = '$userId'";
                
            }
			$rango = $desde != '' ? "AND CAST(v.fecha_presupuesto AS date)>= '$desde'":"AND DATE(fecha_presupuesto) = CURRENT_DATE()";
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, v.anulado AS anulado,  SUM((v.cantidad*v.precio_venta)-(v.cantidad*v.descuento))AS total
			FROM presupuestos v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE 1=1 $vendedor $rango AND v.anulado=0  GROUP BY v.id_presupuesto ORDER BY v.id DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
		public function ListarFiltros($desde)
	{
		try
		{ session_start();
			$userId= $_SESSION['user_id'];
			if(($_SESSION['nivel']<3)){
				$vendedor = "";
            }else{
				$vendedor = "AND v.id_vendedor = '$userId'";
                
            }
			$rango = $desde != '' ? "AND v.fecha_presupuesto >= '$desde'":"";
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, v.anulado AS anulado,  SUM((v.cantidad*v.precio_venta)-(v.cantidad*v.descuento))AS total
			FROM presupuestos v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE 1=1 $vendedor $rango AND v.anulado=0  GROUP BY v.id_presupuesto ORDER BY v.id DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function ListarDetalle($id_presupuesto)
	{
		try
		{
			
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, v.id as id, p.codigo 
			FROM presupuestos v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE id_presupuesto = ? AND v.anulado = '0' ORDER BY v.id DESC");
			$stm->execute(array($id_presupuesto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function ListarPresupuesto($id_presupuesto)
	{
		try
		{
			
			$result = array();

			$stm = $this->pdo->prepare("SELECT SUM((v.cantidad*v.precio_venta)-(v.cantidad*v.descuento))AS total
			FROM presupuestos v 
			LEFT JOIN productos p ON v.id_producto = p.id
			LEFT JOIN usuario u ON v.id_vendedor = u.id
			LEFT JOIN clientes c ON v.id_cliente = c.id
			WHERE id_presupuesto = ? ORDER BY v.id DESC");
			$stm->execute(array($id_presupuesto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function ListarPresupuestoticket($id_presupuesto)
	{
		try
		{
			
			$result = array();

		$stm = $this->pdo->prepare("SELECT *,
                                        IF(LENGTH(p.codigo) > 6, SUBSTRING(p.codigo, -5), p.codigo) AS codigo_producto
                                        FROM presupuestos v
                                        LEFT JOIN productos p ON v.id_producto = p.id
                                        LEFT JOIN usuario u ON v.id_vendedor = u.id
                                        LEFT JOIN clientes c ON v.id_cliente = c.id
                                        WHERE id_presupuesto = ?
                                        ORDER BY v.id DESC");
			$stm->execute(array($id_presupuesto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function ObtenerId_presupuesto($id)
	{
		try 
		{
			$stm = $this->pdo->prepare("SELECT * FROM presupuestos WHERE id_presupuesto = ?");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Obtener()
	{
		try 
		{
		    if(!isset($_SESSION['user_id'])){
				session_start();
			}
		    $user_id = $_SESSION['user_id'];
			$stm = $this->pdo
			          ->prepare("SELECT * FROM presupuestos  WHERE id_vendedor = '$user_id' GROUP BY id_presupuesto");
			          

			$stm->execute();
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
			          ->prepare("SELECT * FROM presupuestos WHERE id_presupuesto = ? LIMIT 1");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	public function ObtenerRegistro($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM presupuestos WHERE id = ? ");
			          

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
			          ->prepare("SELECT MAX(id_presupuesto) as id_presupuesto FROM presupuestos");
			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ObtenerMoneda()
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM monedas WHERE id = 1");
			          

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
			            ->prepare("UPDATE presupuestos SET anulado=1 WHERE id_presupuesto = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Vaciar()
	{
		try 
		{
		    session_start();
		    $id_vendedor = $_SESSION['user_id'];
			$stm = $this->pdo
			            ->prepare("DELETE FROM presupuestos WHERE id_vendedor = ? ");
			$stm->execute(array($id_vendedor));			          

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
			          ->prepare("UPDATE presupuestos SET cantidad = ? WHERE id = ?");
			$stm->execute(array($cantidad, $id_item));
			$stm = $this->pdo
			          ->prepare("SELECT *, (SELECT SUM((precio_venta*cantidad)-(descuento*cantidad)) FROM presupuestos WHERE id_presupuesto = ? GROUP BY id_presupuesto) as total_venta FROM presupuestos WHERE id = ?");
			$stm->execute(array($id_presupuesto, $id_item));
		
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function AnularItem($id_item, $id_presupuesto)
	{
		try 
		{

			$stm = $this->pdo
			          ->prepare("UPDATE presupuestos SET anulado ='1' WHERE  id = ? 
					  AND  id_presupuesto = ( SELECT id_presupuesto FROM presupuestos where id_presupuesto = ? limit 1)");
			$stm->execute(array($id_item, $id_presupuesto));
		
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}



	public function CambiarEstado($data)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE presupuestos SET estado='Vendido' WHERE id_presupuesto = ? ");
			$stm->execute(array($data->id_presupuesto));			          

		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function EditarCantidad($id_item,$cantidad)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE presupuestos SET cantidad = ? WHERE id = ? ");			          

			$stm->execute(array($cantidad, $id_item ));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	public function VentaAnulada($data)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE presupuestos SET estado='NULL' WHERE id_presupuesto = ? ");
			$stm->execute(array($data->id_presupuesto));			          

		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try 
		{
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
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Moneda($data)
	{
		try 
		{
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
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar($data)
	{
		try 
		{
		$sql = "INSERT INTO presupuestos (id_presupuesto, id_cliente, id_vendedor, id_producto,id_sucursal, precio_venta, cantidad, fecha_presupuesto, descuento) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
                    $data->id_presupuesto,
					$data->id_cliente,
                    $data->id_vendedor,
                    $data->id_producto,                 
                    $data->id_sucursal,                 
                    $data->precio_venta,
                    $data->cantidad, 
                    $data->fecha_presupuesto,
					$data->descuento
                   
                )
			);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function CompararProducto($data)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM presupuestos WHERE id_producto = ? AND id_presupuesto = ? AND anulado = 0");
			$stm->execute(array($data->id_producto, $data->id_presupuesto ));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ObtenerTotalPorUsuarioPorMes()
	{
		try
		{ 
			$result = array();
			session_start();
			$userId= $_SESSION['user_id'];

			// Para todos los niveles
			$vendedor = "AND id_vendedor = '$userId'";
                
			// Obtener el mes actual
			$mesActual = date('m');
	
			$stm = $this->pdo->prepare("SELECT *,  SUM((precio_venta*cantidad) - (descuento*cantidad)) AS total 
			FROM presupuestos 
			WHERE 1=1 $vendedor
			AND anulado = 0 
			AND estado = 'vendido'
			AND MONTH(fecha_presupuesto) = $mesActual");
			
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

		public function ObtenerTotalPorUsuarioPorDia()
	{
		try
		{ 
			$result = array();
			session_start();
			$userId= $_SESSION['user_id'];

			// Para todos los niveles
			$vendedor = "AND id_vendedor = '$userId'";
                
			// Obtener la fecha actual
			$fechaActual = date('Y-m-d');
	
			$stm = $this->pdo->prepare("SELECT *, SUM((precio_venta*cantidad) - (descuento*cantidad)) AS total 
        FROM presupuestos 
        WHERE  anulado = 0 AND estado='Vendido' $vendedor AND CAST(fecha_presupuesto AS date) = '$fechaActual'");
			
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

}