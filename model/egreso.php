<?php
class egreso
{
	private $pdo;
    
    public $id;
    public $id_cliente;
    public $id_usuario;
    public $id_compra;
    public $id_acreedor;
    public $fecha;
    public $categoria;
    public $concepto;
    public $comprobante;
	public $nro_comprobante;
    public $monto;
    public $forma_pago;  
    public $sucursal;
    public $anulado;
    public $nro_cheque;
    public $plazo;
    public $id_devolucion;
	public $moneda;
	public $cambio;
	public $tipo_egreso;
	public $fecha_gasto_fijo;
	public $id_gasto_fijo;

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
		     session_start();
		     $id_usuario=$_SESSION['user_id'];
		    if($_SESSION['nivel']==1){
		      	
		      	$result = array();

			    $stm = $this->pdo->prepare("SELECT *, e.id as id, u.user
			    FROM egresos e 
			    LEFT JOIN clientes c ON e.id_cliente = c.id
			    LEFT JOIN usuario u ON e.id_usuario = u.id 
			    WHERE e.anulado IS NULL ORDER BY e.id DESC");
			    $stm->execute();
			    
		    }else{
		   
		     	$result = array();

			$stm = $this->pdo->prepare("SELECT *, e.id as id FROM egresos e LEFT JOIN clientes c ON e.id_cliente = c.id WHERE e.anulado IS NULL AND id_usuario=$id_usuario ORDER BY e.id DESC");
			$stm->execute();
		    }
		

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function ListarExtractoCompras($desde,$hasta)
	{
		try
		{
		     session_start();
		     $id_usuario=$_SESSION['user_id'];
			 $rango=$desde!=''? "AND CAST(e.fecha as date) >='$desde' AND CAST(e.fecha as date) <='$hasta'":"AND MONTH(e.fecha) >='$fecha'";
		      	
		      	$result = array();

			    $stm = $this->pdo->prepare("SELECT *, 
				e.id as id, 
				u.user, 
				c.nombre
			    FROM egresos e 
			    LEFT JOIN clientes c ON e.id_cliente = c.id
			    LEFT JOIN usuario u ON e.id_usuario = u.id 
			    WHERE e.anulado IS NULL AND e.tipo_egreso='COMPRA' $rango   ORDER BY e.id DESC");
			    $stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	
	public function ListarExtractoMovimiento($desde,$hasta)
	{
		try
		{
		     session_start();
		     $id_usuario=$_SESSION['user_id'];
			 $fecha=date('m');
			 $rango=$desde!=''? "AND CAST(e.fecha as date) >='$desde' AND CAST(e.fecha as date) <='$hasta'":"AND MONTH(e.fecha) >='$fecha'";
		      	
		      	$result = array();

			    $stm = $this->pdo->prepare("SELECT *, 
				e.id as id, 
				u.user, 
				c.nombre
			    FROM egresos e 
			    LEFT JOIN clientes c ON e.id_cliente = c.id
			    LEFT JOIN usuario u ON e.id_usuario = u.id 
			    WHERE e.anulado IS NULL AND e.tipo_egreso='MOVIMIENTOS' $rango   ORDER BY e.id DESC");
			    $stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function ListarExtractoVariable($desde,$hasta)
	{
		try
		{
		     session_start();
		     $id_usuario=$_SESSION['user_id'];
			 $fecha=date('m');
			 $rango=$desde!=''? "AND CAST(e.fecha as date) >='$desde' AND CAST(e.fecha as date) <='$hasta'":"AND MONTH(e.fecha) >='$fecha'";
		      	
		      	$result = array();

			    $stm = $this->pdo->prepare("SELECT *, 
				e.id as id, 
				u.user, 
				c.nombre
			    FROM egresos e 
			    LEFT JOIN clientes c ON e.id_cliente = c.id
			    LEFT JOIN usuario u ON e.id_usuario = u.id 
			    WHERE e.anulado IS NULL AND e.tipo_egreso='GASTO VARIABLE' $rango   ORDER BY e.id DESC");
			    $stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function ListarExtractoGastosFijos($desde,$hasta)
	{
		try
		{
		     session_start();
		     $id_usuario=$_SESSION['user_id'];
			 $fecha=date('m');
			 $rango=$desde!=''? "AND CAST(e.fecha as date) >='$desde' AND CAST(e.fecha as date) <='$hasta'":"AND MONTH(e.fecha) >='$fecha'";
		      	
		      	$result = array();

			    $stm = $this->pdo->prepare("SELECT *, 
				e.id as id, 
				u.user, 
				c.nombre
			    FROM egresos e 
			    LEFT JOIN clientes c ON e.id_cliente = c.id
			    LEFT JOIN usuario u ON e.id_usuario = u.id 
			    WHERE e.anulado IS NULL AND (e.tipo_egreso='GASTO FIJO' OR e.id_gasto_fijo > 0) $rango   ORDER BY e.id DESC");
			    $stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function ListarExtractoGasto($desde,$hasta)
	{
		try
		{
		     session_start();
		     $id_usuario=$_SESSION['user_id'];
			 $fecha=date('m');
			 $rango=$desde!=''? "AND CAST(e.fecha as date) >='$desde' AND CAST(e.fecha as date) <='$hasta'":"AND MONTH(e.fecha) >='$fecha'";
		      	
		      	$result = array();

			    $stm = $this->pdo->prepare("SELECT *, 
				e.id as id, 
				u.user, 
				c.nombre
			    FROM egresos e 
			    LEFT JOIN clientes c ON e.id_cliente = c.id
			    LEFT JOIN usuario u ON e.id_usuario = u.id 
			    WHERE e.anulado IS NULL AND e.tipo_egreso='GASTO' $rango   ORDER BY e.id DESC");
			    $stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function MiLista()
	{
		try
		{
			session_start();

			$stm = $this->pdo->prepare("SELECT *, e.id as id FROM egresos e LEFT JOIN clientes c ON e.id_cliente = c.id
			    WHERE id_usuario = ? AND anulado IS NULL
			    ORDER BY e.id DESC");
			$stm->execute(array($_SESSION['user_id']));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function MiListaAnulados()
	{
		try
		{
			session_start();

			$stm = $this->pdo->prepare("SELECT *, e.id as id FROM egresos e LEFT JOIN clientes c ON e.id_cliente = c.id
			    WHERE id_usuario = ? AND anulado = 1
			    ORDER BY e.id DESC");
			$stm->execute(array($_SESSION['user_id']));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function ListarCategoria()
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT categoria FROM egresos GROUP BY categoria");
			$stm->execute();
			return $stm->fetchALL(PDO::FETCH_OBJ);
			
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarSinAnular()
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, e.id as id FROM egresos e LEFT JOIN clientes c ON e.id_cliente = c.id WHERE anulado IS NULL ORDER BY e.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}


	public function ListarAcreedor($id_acreedor)
	{
		try
		{
			$result = array();
			$stm = $this->pdo->prepare("SELECT *, e.id as id FROM egresos e LEFT JOIN clientes c ON e.id_cliente = c.id WHERE e.id_acreedor = ?  AND e.anulado IS NULL  ORDER BY e.id DESC");
			$stm->execute(array($id_acreedor));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarSesion($id_usuario)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM egresos WHERE categoria <> 'Venta' AND fecha >= (SELECT fecha_apertura FROM cierres WHERE id_usuario = ? AND fecha_cierre IS NULL) AND anulado IS NULL  ORDER BY id DESC");
			$stm->execute(array($id_usuario));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarRangoSesion($desde,$hasta,$id_usuario)
	{
		try
		{
			$result = array();
			$fecha=date('Y-m-d');
			if ($desde != '') {
				if ($hasta != '') {
					$rango = " AND CAST(e.fecha as date) >= '$desde' AND CAST(e.fecha as date) <= '$hasta'";
				} else {
					$rango = " AND CAST(e.fecha as date) >= '$desde' AND CAST(e.fecha as date) <= '$fecha'";
				}
			}
			if ($id_usuario != '') {
					$user = " AND e.id_usuario = '$id_usuario'";
			}else {
					$user = " ";
			}

			$stm = $this->pdo->prepare("SELECT *, e.id as id, c.nombre FROM egresos e 
			LEFT JOIN clientes c ON e.id_cliente = c.id 
			WHERE  e.anulado IS NULL  $rango $user
			ORDER BY e.id DESC ");
			$stm->execute(array($desde,$hasta,$id_usuario));

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
			$stm = $this->pdo
			          ->prepare("SELECT * FROM egresos WHERE MONTH(fecha) = MONTH(?) AND YEAR(fecha) = YEAR(?) AND categoria <> 'compra'  AND anulado IS NULL AND MONTH(fecha) = '6' AND DAY(fecha) = '1' ");
			          

			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	public function AgrupadoMes($desde, $hasta, $anho)
	{
		try
		{
			$result = array();
			$mes = date('Y-m').'-01';
			$fecha=date('Y-m-d');

			if($anho > 0){
			   $anho = "AND YEAR(e.fecha) = $anho";
			   $rango = "";
			}else{
				$anho = "";

					if($desde != ''){
						if($hasta!=''){
							$rango = " AND CAST(e.fecha as date) >= '$desde' AND CAST(e.fecha as date) <= '$hasta'";
						}else{
							$rango = " AND CAST(e.fecha as date) >= '$desde' AND CAST(e.fecha as date) <= '$fecha'";  
						}
					}else{
						if($hasta!=''){
								$rango = " AND CAST(e.fecha as date) >= '$mes' AND CAST(e.fecha as date) <= '$hasta'";
						}else{
							$rango = " AND CAST(e.fecha as date) >= '$mes' AND CAST(e.fecha as date) <= '$fecha'";  
						}
					}   
            }

			// die("SELECT e.categoria, e.fecha, SUM(e.monto) as monto FROM egresos e WHERE e.anulado IS NULL $rango $anho AND e.categoria <> 'Transferencia' GROUP BY e.categoria ORDER BY e.id DESC");

			$sql = "SELECT e.categoria, e.fecha, SUM(e.monto) as monto, e.moneda FROM egresos e WHERE e.anulado IS NULL $rango $anho AND e.categoria <> 'Transferencia' GROUP BY e.categoria ORDER BY e.id DESC";
			          
			$stm = $this->pdo->prepare($sql);
			$stm->execute();
			return $stm->fetchAll(PDO::FETCH_OBJ);
			}
		
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	
	public function AgrupadoFechaMes($fecha)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT categoria, SUM(monto) as monto FROM egresos WHERE MONTH(fecha) = MONTH(?) AND YEAR(fecha) = YEAR(?) AND anulado IS NULL AND categoria <> 'Transferencia' AND categoria <> 'compra' AND categoria <> 'compras' GROUP BY categoria ORDER BY id DESC");
			          

			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
		public function Listar_rango($desde, $hasta, $nombre, $categoria, $metodo, $tipo_egreso, $id_caja)
	{
		try
		{
			$result = array();
			session_start();
			$nivel=$_SESSION['nivel'];
			$user_id=$_SESSION['user_id'];
			$fecha=date('m');
			
		   
		    $user=$nombre!=''? "AND  e.id_cliente='$nombre'":"";
		    $usuario=$nivel != 1 ? "AND  e.id_usuario='$user_id'":"";
			$rango=$desde!=''? "AND CAST(e.fecha as date) >='$desde' AND CAST(e.fecha as date) <='$hasta'":"AND MONTH(e.fecha) >='$fecha'";
			$cat=$categoria!=''? "AND  e.categoria='$categoria'":"";
			$m=$metodo!=''? "AND  e.forma_pago='$metodo'":"";
			$tipo=$tipo_egreso!=''? "AND  e.tipo_egreso='$tipo_egreso'":"";
			$caja=$id_caja!=''? "AND  e.id_caja='$id_caja'":"";

			
		
			$stm = $this->pdo->prepare("SELECT *, e.id as id, u.user
			FROM egresos e 
			LEFT JOIN clientes c ON e.id_cliente = c.id
			LEFT JOIN usuario u ON e.id_usuario = u.id 
			WHERE e.anulado IS NULL $user $usuario $rango $cat $m $tipo $caja ORDER BY e.id DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
		public function ListarSincompra($fecha)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM egresos WHERE categoria <> 'compra' AND Cast(fecha as date) = ? AND anulado IS NULL ORDER BY id DESC");
			$stm->execute(array($fecha));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarSinCompraMes($desde, $hasta)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM egresos WHERE CAST(fecha AS date) >= ? AND CAST(fecha AS date) <= ? AND id_acreedor IS NULL AND anulado IS NULL AND categoria <> 'compra' AND categoria <> 'Devolución' ORDER BY id ASC");
			$stm->execute(array($desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

public function ListarDevoluciones($desde, $hasta)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM egresos WHERE CAST(fecha AS date) >= ? AND CAST(fecha AS date) <= ? AND id_acreedor IS NULL AND anulado IS NULL AND categoria = 'Devolución' ORDER BY id ASC");
			$stm->execute(array($desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function EditarMonto($id_compra, $monto)
	{
		try 
		{
			$sql = "UPDATE egresos SET 
						monto    = ?
				    WHERE id_compra = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$monto,
                        $id_compra
					)
				);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Agrupado_egreso($mes)
	{
		try
		{
			$result = array();
			if($mes!='0'){
				$stm = $this->pdo->prepare("SELECT concepto, categoria, sum(monto) as monto, fecha  FROM egresos WHERE MONTH(fecha) = $mes AND anulado IS NULL AND categoria <> 'Transferencia' GROUP BY categoria ORDER BY id DESC");
			}else{
				$stm = $this->pdo->prepare("SELECT concepto, categoria, sum(monto) as monto FROM egresos WHERE anulado IS NULL AND categoria <> 'Transferencia' GROUP BY categoria ORDER BY id DESC");	
			}
			$stm->execute();

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
			          ->prepare("SELECT *, e.id AS id
			          FROM egresos e
			          LEFT JOIN clientes c ON e.id_cliente = c.id 
			          WHERE e.id = ?");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

    public function ActualizarCompra($id_compra)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE egresos e SET e.monto = (SELECT SUM(c.total) FROM compras c WHERE c.id_compra = ?) WHERE id_compra = ?");			          

			$stm->execute(array($id_compra, $id_compra));
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
			            ->prepare("UPDATE egresos SET anulado = 1 WHERE id = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	public function EliminarDevolucion($id)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE egresos SET anulado = 1 WHERE id_devolucion = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	public function Anularcompra($id)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("DELETE FROM egresos WHERE id_compra = ?");			          

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
			$sql = "UPDATE egresos SET 
			            id_cliente     = ?,
						id_caja        = ?,
			            id_compra      = ?,
			            fecha      	   = ?,
						categoria      = ?,
						concepto       = ?,
						comprobante    = ?,
						nro_comprobante = ?, 
						monto          = ?, 
						forma_pago     = ?,
                        sucursal       = ?,
                        nro_cheque     = ?,
                        plazo          = ?,
                        id_devolucion  = ?,
						moneda         = ?,
						cambio         = ?,
						tipo_egreso    = ?,
						id_gasto_fijo  = ?,
						fecha_gasto_fijo = ?
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				        $data->id_cliente,
						$data->id_caja,
				        $data->id_compra,
				    	$data->fecha,
                        $data->categoria, 
                        $data->concepto, 
                        $data->comprobante,
						$data->nro_comprobante,                        
                        $data->monto,
                        $data->forma_pago,
                        $data->sucursal,
                        $data->nro_cheque,
                        $data->plazo,
                        $data->id_devolucion,
						$data->moneda,
						$data->cambio,
						$data->tipo_egreso,
						$data->id_gasto_fijo,
						$data->fecha_gasto_fijo,

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
			session_start();
			$id_usuario = $_SESSION['user_id'];

			$sql = "INSERT INTO egresos (id_cliente, id_usuario, id_caja, id_compra, id_acreedor, fecha, categoria, concepto, comprobante, nro_comprobante, monto, forma_pago, sucursal, nro_cheque, plazo, id_devolucion, moneda, cambio, tipo_egreso, id_gasto_fijo, fecha_gasto_fijo) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


		$this->pdo->prepare($sql)
		     ->execute(
				array(
				    $data->id_cliente,
					$data->id_usuario,
				    $data->id_caja,
					$data->id_compra,
					$data->id_acreedor,
					$data->fecha,
                	$data->categoria, 
                	$data->concepto, 
                	$data->comprobante,
					$data->nro_comprobante,                        
                	$data->monto,
                	$data->forma_pago,
                	$data->sucursal,
                	$data->nro_cheque,
                	$data->plazo,
                	$data->id_devolucion,
					$data->moneda,
					$data->cambio,
					$data->tipo_egreso,
					$data->id_gasto_fijo,
					$data->fecha_gasto_fijo

                   
                )
			);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}