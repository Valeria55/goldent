<?php
class deuda
{
	private $pdo;
    
    public $id;
    public $id_cliente;
    public $id_venta;
    public $fecha;
    public $vencimiento;
    public $concepto;
    public $monto;
    public $saldo;
    public $sucursal;  
    
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

			$stm = $this->pdo->prepare("SELECT *, d.id as id, c.id as id_cliente FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE saldo > 0 ORDER BY d.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarDeudaAgrupado()
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, d.id as id, c.id as id_cliente, SUM(monto) as montos, SUM(saldo) AS saldos FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE saldo > 0  GROUP BY d.id_cliente ORDER BY d.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}


	
	
	public function ListarAgrupadoCliente()
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, d.id as id, c.id as id_cliente, SUM(monto) as monto, SUM(saldo) AS saldo FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE saldo > 0 GROUP BY d.id_cliente ORDER BY d.id DESC");
			$stm->execute();

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
			$stm = $this->pdo
			          ->prepare("SELECT * FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE Cast(fecha as date) = ?");
			          

			$stm->execute(array($fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	public function ListarMes($fecha)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE MONTH(fecha) = MONTH(?) AND YEAR(fecha) = YEAR(?)");
			          

			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
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
			          ->prepare("SELECT *, d.id FROM deudas d LEFT JOIN clientes c ON d.id_cliente = c.id WHERE d.id = ?");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ObtenerTodos($id_cliente)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT *, d.id, SUM(d.saldo) as saldototal
			          	FROM deudas d 
			          	LEFT JOIN clientes c ON d.id_cliente = c.id 
			          	WHERE d.id_cliente = ?
						GROUP BY d.id_cliente");
			          

			$stm->execute(array($id_cliente));
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
			          ->prepare("SELECT MAX(id) as id FROM deudas");
			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function listar_cliente($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM deudas WHERE id_cliente = ? AND saldo > 0");
			          

			$stm->execute(array($id));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}


	public function listarExtracto($id_cliente, $desde, $hasta)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT 
					  i.id_cliente,
					  i.id_venta,
					  i.id_deuda,
					  i.fecha,
					  i.categoria, 
					  i.concepto,
					  i.monto,
					  i.moneda,
					  i.cambio,
					  'Ingreso' AS descripcion
			  
				  FROM ingresos i
				  WHERE i.id_cliente = ? AND (i.categoria = 'Cobro de deuda' OR i.categoria ='Entrega')
				  
					  UNION ALL
					  
			  SELECT 
					  d.id_cliente,
					  d.id_venta,
					  d.id as id_deuda,
					  d.fecha,
					  '' AS categoria, 
					  d.concepto,
					  d.monto,
					  d.moneda,
					  d.cambio,
					  'Deuda' AS descripcion
			  
				  FROM deudas d
				  WHERE d.id_cliente = ?

				 ORDER BY fecha ASC; 
				  ");
			          

			$stm->execute(array($id_cliente, $id_cliente));
			return $stm->fetchAll(PDO::FETCH_OBJ);
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
			            ->prepare("DELETE FROM deudas WHERE id = ?");			          

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
			            ->prepare("DELETE FROM deudas WHERE id_venta = ?");			          

			$stm->execute(array($id));
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

			$sql = "SELECT i.concepto, i.fecha, SUM(i.monto) as monto, SUM(i.saldo) as saldo, c.nombre AS nombre 
			FROM deudas i
			LEFT JOIN clientes c ON c.id=i.id_cliente
			WHERE saldo > 0 $rango $anho 
			GROUP BY i.id_cliente ORDER BY i.id DESC";
			          
			$stm = $this->pdo->prepare($sql);
			$stm->execute();
			return $stm->fetchAll(PDO::FETCH_OBJ);
			}
		
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ObtenerDeudas($id,$id_venta)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT SUM(saldo) AS saldo
			          FROM deudas d 
			          WHERE d.id_cliente = ? AND id_venta <> ?");
			          

			$stm->execute(array($id,$id_venta));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ObtenerDeudaCliente($id_cliente)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT d.saldo, d.id as id
			          	FROM deudas d 
			          	WHERE d.id_cliente = ?
						");
			          
			$stm->execute(array($id_cliente));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function SumarSaldo($data)
	{
		try 
		{
			$sql = "UPDATE deudas SET saldo = saldo + ? WHERE id = ?";

			$this->pdo->prepare($sql)
				->execute(
				    array($data->monto, $data->id)
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
			$sql = "UPDATE deudas SET 
						id_cliente    = ?,
						id_venta      = ?,
						fecha      	  = ?,
						vencimiento	  = ?,
						concepto      = ?, 
						monto         = ?,
						moneda         = ?,
						cambio         = ?,
						saldo         = ?,
						sucursal      = ?
                        
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$data->id_cliente,
                        $data->id_venta,
                        $data->fecha,
                        $data->vencimiento,
                        $data->concepto,                        
                        $data->monto,
                        $data->moneda,
                        $data->cambio,
                        $data->saldo,
                        $data->sucursal,
                        $data->id
					)
				);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}


	public function EditarMonto($id_venta, $monto)
	{
		try 
		{
			$sql = "UPDATE deudas SET 
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
	
	public function Restar($saldo, $id)
	{
		try 
		{
			$sql = "UPDATE deudas SET 
					
					saldo = ?
                        
				    WHERE id = ?";

				$this->pdo->prepare($sql)
					->execute(
						array(                       
							$saldo,
							$id
						)
					);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	public function Guardar($data)
	{
		try 
		{
		$sql = "INSERT INTO deudas (id_cliente, id_venta, fecha, vencimiento, concepto, monto, saldo, sucursal) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->id_cliente,
					$data->id_venta,
					$data->fecha,
					$data->vencimiento,
					$data->concepto,
                    $data->monto,
                    $data->saldo,
                    $data->sucursal
                    
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
		$sql = "INSERT INTO deudas (id_cliente, id_venta, fecha, vencimiento, concepto, monto, moneda, cambio, saldo, sucursal) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->id_cliente,
					$data->id_venta,
					$data->fecha,
					$data->vencimiento,
					$data->concepto,
                    $data->monto,
                    $data->moneda,
                    $data->cambio,
                    $data->saldo,
                    $data->sucursal
                    
                )
			);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}