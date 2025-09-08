<?php
class ingreso
{
	private $pdo;
    
    public $id;
    public $id_cliente;
    public $id_usuario;
    public $id_caja;
    public $id_venta;
    public $id_deuda;
    public $fecha;
    public $categoria;
    public $concepto;
    public $comprobante;
	public $nro_comprobante;
    public $monto;
    public $forma_pago;  
    public $sucursal;
    public $anulado;
    public $id_gift;
	public $moneda;
	public $cambio;

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
	public function Listar($fecha)
	{
		try
		{
		     session_start();
		     $id_usuario=$_SESSION['user_id'];
		    if($_SESSION['nivel']==1){
		      	
		      	$result = array();

				  $stm = $this->pdo->prepare("SELECT *, i.id as id, 
				  (SELECT v.id_presupuesto FROM ventas v WHERE v.id_venta=i.id_venta LIMIT 1) AS id_presupuesto 
				  FROM ingresos i 
				  LEFT JOIN clientes c ON i.id_cliente = c.id 
				  LEFT JOIN usuario u ON i.id_usuario = u.id 
				  WHERE cast(i.fecha as date) = ? 
				  ORDER BY i.fecha DESC");
				  $stm->execute(array($fecha));
			    
		    }else{
		   
		     	$result = array();

				 $stm = $this->pdo->prepare("SELECT *, i.id as id, (SELECT v.id_presupuesto FROM ventas v WHERE v.id_venta=i.id_venta LIMIT 1) AS id_presupuesto  FROM ingresos i 
				 LEFT JOIN clientes c ON i.id_cliente = c.id 
				 WHERE i.id_usuario=$id_usuario AND cast(i.fecha as date) = ? 
				 ORDER BY i.fecha DESC");
				 $stm->execute(array($fecha));
		    }
		

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	public function ListarExtracto($desde,$hasta)
	{
		try
		{
		     session_start();
		     $id_usuario=$_SESSION['user_id'];
			 $fecha=date('m');
			 $rango=$desde!=''? "AND CAST(i.fecha as date) >='$desde' AND CAST(i.fecha as date) <='$hasta'":"AND MONTH(i.fecha) >='$fecha'";
		      	
		      	$result = array();

			    $stm = $this->pdo->prepare("SELECT *,SUM(monto) AS monto, 
				i.id as id, 
				u.user, 
				c.nombre
			    FROM ingresos i 
			    LEFT JOIN clientes c ON i.id_cliente = c.id
			    LEFT JOIN usuario u ON i.id_usuario = u.id 
			    WHERE i.anulado IS NULL AND i.id_venta > 0 $rango GROUP BY i.forma_pago ORDER BY i.id DESC");
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

			$stm = $this->pdo->prepare("SELECT SUM(round(monto*cambio, 2)) AS total FROM ingresos WHERE id_venta = ?");
			$stm->execute(array($id_venta));
			return $stm->fetch(PDO::FETCH_OBJ);
			
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

			$stm = $this->pdo->prepare("SELECT categoria FROM ingresos GROUP BY categoria");
			$stm->execute();
			return $stm->fetchALL(PDO::FETCH_OBJ);
			
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarVenta($id)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, (SELECT v.nro_comprobante FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as nro_comprobante, (SELECT v.banco FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as banco, i.id as id FROM ingresos i LEFT JOIN clientes c ON i.id_cliente = c.id WHERE i.id = ? ORDER BY i.id DESC");
			$stm->execute(array($id));

			return $stm->fetch(PDO::FETCH_OBJ);
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

			$stm = $this->pdo->prepare("SELECT *, i.id as id FROM ingresos i LEFT JOIN clientes c ON i.id_cliente = c.id
			    WHERE id_usuario = ? AND anulado IS NULL
			    ORDER BY i.id DESC");
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

			$stm = $this->pdo->prepare("SELECT *, i.id as id FROM ingresos i LEFT JOIN clientes c ON i.id_cliente = c.id
			    WHERE id_usuario = ? AND anulado = 1
			    ORDER BY i.id DESC");
			$stm->execute(array($_SESSION['user_id']));

			return $stm->fetchAll(PDO::FETCH_OBJ);
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

			$stm = $this->pdo->prepare("SELECT *, i.id as id FROM ingresos i LEFT JOIN clientes c ON i.id_cliente = c.id WHERE anulado IS NULL ORDER BY i.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}


	public function ListarDeuda($id_cliente)
	{
		try
		{
			$result = array();
			$stm = $this->pdo->prepare("SELECT *, i.id as id
			FROM ingresos i 
			LEFT JOIN clientes c ON i.id_cliente = c.id 
			WHERE i.id_cliente = ? AND (i.categoria = 'Cobro de deuda' OR i.categoria = 'Entrega') AND  i.anulado IS NULL ORDER BY i.id DESC");
			$stm->execute(array($id_cliente));

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
			          ->prepare("SELECT * FROM ingresos WHERE MONTH(fecha) = MONTH(?) AND YEAR(fecha) = YEAR(?) AND categoria <> 'Venta' ");
			          

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
				$anho = "AND YEAR(i.fecha) = $anho";
				$rango = "";
				
			}else{
                $anho = "";
        
				if($desde != ''){
					if($hasta!=''){
						$rango = " AND CAST(i.fecha as date) >= '$desde' AND CAST(i.fecha as date) <= '$hasta'";
					}else{
						$rango = " AND CAST(i.fecha as date) >= '$desde' AND CAST(i.fecha as date) <= '$fecha'";  
					}
				}else{
					if($hasta!=''){
						$rango = " AND CAST(i.fecha as date) >= '$mes' AND CAST(i.fecha as date) <= '$hasta'";
					}else{
						$rango = " AND CAST(i.fecha as date) >= '$mes' AND CAST(i.fecha as date) <= '$fecha'";  
					}
				}   
            }


			// die("SELECT i.categoria, i.fecha, SUM(i.monto) as monto FROM ingresos i WHERE anulado IS NULL $rango $anho AND i.categoria <> 'Transferencia' AND i.categoria <> 'Venta por gift card' GROUP BY i.categoria ORDER BY i.id DESC");

			$sql = "SELECT i.categoria, i.forma_pago, i.fecha, SUM(i.monto) as monto, i.moneda, i.cambio FROM ingresos i WHERE anulado IS NULL $rango $anho AND i.categoria <> 'Transferencia' AND i.categoria <> 'Venta por gift card' GROUP BY i.categoria, i.forma_pago, i.moneda ORDER BY i.forma_pago ASC";

			$stm = $this->pdo->prepare($sql);
			$stm->execute();
			return $stm->fetchAll(PDO::FETCH_OBJ);
			}
		
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function Listar_rango($desde,$hasta,$categoria, $persona, $metodo)
	{
		try
		{
			$result = array();
			session_start();
			$nivel=$_SESSION['nivel'];
			$user_id=$_SESSION['user_id'];
			$fecha=date('Y-m-d');
			
			    $user=$nivel != 1 ? "AND  i.id_usuario='$user_id'":"";
				$rango=$desde!=''? "AND CAST(i.fecha as date) >='$desde' AND CAST(i.fecha as date) <='$hasta'":"AND CAST(i.fecha as date) >='$fecha'";
				$cat=$categoria!=''? "AND  i.categoria='$categoria'":"";
				$p=$persona!=''? "AND  i.id_cliente='$persona'":"";
				$m=$metodo!=''? "AND  i.forma_pago='$metodo'":"";

			$stm = $this->pdo->prepare("SELECT *, i.id as id, u.user,
			(SELECT v.id_presupuesto FROM ventas v WHERE v.id_venta=i.id_venta LIMIT 1 ) AS id_presupuesto 
			FROM ingresos i
			LEFT JOIN clientes c ON i.id_cliente = c.id 
			LEFT JOIN usuario u ON i.id_usuario = u.id 
			WHERE i.anulado is NULL $rango $cat $p $user $m
			ORDER BY i.id DESC");
			$stm->execute(array());

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
		public function ListarEntradaSalida($desde,$hasta,$id_persona)
	{
		try
		{
			$result = array();
			$mes=date('m');
			if ($desde != '') {
				if ($hasta != '') {
					$rango = " AND CAST(fecha as date) >= '$desde' AND CAST(fecha as date) <= '$hasta'";
				} else {
					$rango = " AND CAST(fecha as date) >= '$desde' AND CAST(fecha as date) <= '$fecha'";
				}
			}else{
			    $rango = " AND MONTH(fecha)=$mes";
			}
			if ($id_persona != '') {
					$persona = " AND id_cliente = '$id_persona'";
			}else {
					$persona = " ";
			}

			$stm = $this->pdo->prepare("
			SELECT 
			i.fecha,
			i.categoria, 
			i.concepto,
			i.id as id,
			i.comprobante,
			i.nro_comprobante,
			0 AS egreso, 
			i.monto AS ingreso,
		    i.monto,
			i.forma_pago,
			i.anulado,
			i.moneda,
			i.cambio,
			s.user 
			FROM ingresos i 
			LEFT JOIN clientes c ON i.id_cliente = c.id 
			LEFT JOIN usuario s ON s.id = i.id_usuario
			WHERE i.anulado IS NULL AND i.categoria='VALE'$rango $persona 
			
			UNION ALL
			
			SELECT e.fecha,
			e.categoria,
			e.concepto,
			e.id as id,
			e.comprobante,
			e.nro_comprobante,
			(e.monto * -1 ) AS egreso,
			0 AS ingreso,
			(e.monto * -1 ) AS monto,
			e.forma_pago,
			e.anulado,
			e.moneda,
			e.cambio,
			s.user 
			FROM egresos e 
			LEFT JOIN clientes c ON e.id_cliente = c.id 
			LEFT JOIN usuario s ON s.id = e.id_usuario
			WHERE  e.anulado IS NULL AND e.categoria='VALE'$rango $persona 
			ORDER BY fecha DESC");
			$stm->execute(array());

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
			
			if ($desde != '') {
				if ($hasta != '') {
					$rango = " AND CAST(i.fecha as date) >= '$desde' AND CAST(i.fecha as date) <= '$hasta'";
				} else {
					$rango = " AND CAST(i.fecha as date) >= '$desde' AND CAST(i.fecha as date) <= '$fecha'";
				}
			}
			if ($id_usuario != '') {
					$user = " AND i.id_usuario = '$id_usuario'";
			}else {
					$user = " ";
			}

			$stm = $this->pdo->prepare("SELECT *, i.id as id FROM ingresos i LEFT JOIN clientes c ON i.id_cliente = c.id WHERE    (i.id_venta = 0 OR i.categoria='Cobro de deuda')  AND i.anulado IS NULL $rango $user ORDER BY i.id DESC");
			$stm->execute(array($desde,$hasta,$id_usuario));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarSinVenta($fecha)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM ingresos WHERE categoria <> 'Venta' AND Cast(fecha as date) = ? AND anulado IS NULL ORDER BY id DESC");
			$stm->execute(array($fecha));

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

			$stm = $this->pdo->prepare("SELECT * FROM ingresos WHERE categoria <> 'Venta' AND fecha >= (SELECT fecha_apertura FROM cierres WHERE id_usuario = ? AND fecha_cierre IS NULL) ORDER BY id DESC");
			$stm->execute(array($id_usuario));

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

			$stm = $this->pdo->prepare("SELECT *, (SELECT ( ( (SUM(precio_venta*cantidad)-SUM(precio_costo*cantidad)) * 100 )/ SUM(precio_venta*cantidad)) AS ganancia FROM ventas v WHERE v.id_venta = i.id_venta GROUP BY id_venta) AS margen_ganancia FROM ingresos i WHERE id_deuda IS NOT NULL AND CAST(fecha AS date) >= ? AND CAST(fecha AS date) <= ? AND anulado IS NULL ORDER BY id ASC");
			$stm->execute(array($desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function VentasContadoMoneda($desde, $hasta)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, 
			(SELECT ( ( (SUM(precio_venta*cantidad)-SUM(precio_costo*cantidad)) * 100 )/ SUM(precio_venta*cantidad)) AS ganancia FROM ventas v WHERE v.id_venta = i.id_venta GROUP BY id_venta) AS margen_ganancia, SUM(i.monto) AS monto
			FROM ingresos i WHERE id_deuda IS NULL AND CAST(fecha AS date) >= ? AND CAST(fecha AS date) <= ? AND anulado IS NULL AND id_venta > 0 GROUP BY i.moneda, i.id_venta ORDER BY id ASC;");
			$stm->execute(array($desde, $hasta));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}



	public function EditarMonto($id_venta, $monto)
	{
		try 
		{
			$sql = "UPDATE ingresos SET 
						monto    = ?
				    WHERE id_venta = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$monto,
                        $id_venta
					)
				);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Agrupado_ingreso($mes)
	{
		try
		{
			$result = array();
			if($mes!='0'){
				$stm = $this->pdo->prepare("SELECT concepto, categoria, sum(monto) as monto, fecha  FROM ingresos WHERE MONTH(fecha) = $mes AND anulado IS NULL AND categoria <> 'Transferencia' GROUP BY categoria ORDER BY id DESC");
			}else{
				$stm = $this->pdo->prepare("SELECT concepto, categoria, sum(monto) as monto FROM ingresos WHERE anulado IS NULL AND categoria <> 'Transferencia' GROUP BY categoria ORDER BY id DESC");	
			}
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	
	public function ObtenerIngreso($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM ingresos WHERE id_gift = ?");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ObtenerVenta($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM ingresos WHERE id_venta = ? and categoria = 'entrega'");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	public function ObtenerCobro($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT *,SUM(monto) AS monto, (SELECT v.id_presupuesto FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) AS id_presupuesto FROM ingresos i WHERE i.id_venta = ? AND i.anulado IS NULL GROUP BY forma_pago,moneda");
			$stm->execute(array($id));
			return $stm->fetchALL(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	public function ObtenerExtra($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT *, SUM(i.monto/ i.cambio) as monto_total FROM ingresos i WHERE id_venta = ?");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	public function ObtenerGift($id_venta)
	{
		try 
		{
			$stm = $this->pdo->prepare("SELECT monto FROM ingresos WHERE id_venta = ? AND forma_pago = 'Gift Card'");
			          

			$stm->execute(array($id_venta));
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
			          ->prepare("SELECT * FROM ingresos WHERE id = ?");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	
	public function UltimoID()
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT id FROM ingresos ORDER BY id desc LIMIT 1");
			          

			$stm->execute(array());
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
			            ->prepare("UPDATE ingresos SET anulado = 1 WHERE id = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	public function AnularVenta($id)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE ingresos SET anulado = 1 WHERE id_venta = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	public function EliminarVenta($id)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("DELETE FROM ingresos WHERE id_venta = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	public function AnularGift($id)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE ingresos SET anulado = 1 WHERE id_gift = ?");			          

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
			$sql = "UPDATE ingresos SET 
			            id_cliente     = ?,
						id_caja        = ?,
			            id_venta       = ?,
			            fecha      	   = ?,
						categoria      = ?,
						concepto       = ?,
						comprobante    = ?, 
						nro_comprobante    = ?,
						monto          = ?, 
						forma_pago     = ?,
                        sucursal       = ?,
                        id_gift        = ?,
                        tarjeta        = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				        $data->id_cliente,
						$data->id_caja,
				        $data->id_venta,
				    	$data->fecha,
                        $data->categoria, 
                        $data->concepto, 
                        $data->comprobante,  
						$data->nro_comprobante,                      
                        $data->monto,
                        $data->forma_pago,
                        $data->sucursal,
                        $data->id_gift,
                        $data->tarjeta,
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
			if($data->categoria=="Transferencia"){
				$id_usuario = $data->id_usuario;
			}else{
				$id_usuario = $_SESSION['user_id'];
			}

		$sql = "INSERT INTO ingresos (id_cliente, id_usuario, id_caja, id_venta, id_deuda, fecha, categoria, concepto, comprobante,nro_comprobante, monto, forma_pago, sucursal, id_gift, moneda, cambio, tarjeta) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
				    $data->id_cliente,
					$id_usuario,
					$data->id_caja,
					$data->id_venta,
					$data->id_deuda,
					$data->fecha,
                	$data->categoria, 
                	$data->concepto, 
                	$data->comprobante, 
					$data->nro_comprobante,                       
                	$data->monto,
                	$data->forma_pago,
                	$data->sucursal,
                	$data->id_gift,
					$data->moneda,
					$data->cambio,
					$data->tarjeta
                   
                )
			);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}