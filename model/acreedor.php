<?php
class acreedor
{
	private $pdo;
    
    public $id;
    public $id_cliente;
    public $id_compra;
    public $fecha;
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

	public function ListarAPagar()
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, a.id as id, c.id as id_cliente FROM acreedores a LEFT JOIN clientes c ON a.id_cliente = c.id WHERE saldo > 0 ORDER BY a.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarPagados()
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, a.id as id, c.id as id_cliente FROM acreedores a LEFT JOIN clientes c ON a.id_cliente = c.id WHERE saldo <= 0 ORDER BY a.id DESC");
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
			          ->prepare("SELECT * FROM acreedores a LEFT JOIN clientes c ON a.id_cliente = c.id WHERE Cast(fecha as date) = ?");
			          

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
			          ->prepare("SELECT * FROM acreedores a LEFT JOIN clientes c ON a.id_cliente = c.id WHERE MONTH(fecha) = MONTH(?) AND YEAR(fecha) = YEAR(?)");
			          

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
			          ->prepare("SELECT *, a.id FROM acreedores a LEFT JOIN clientes c ON a.id_cliente = c.id WHERE a.id = ?");
			          

			$stm->execute(array($id));
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
			          ->prepare("SELECT * FROM acreedores WHERE id_cliente = ? AND saldo > 0");
			          

			$stm->execute(array($id));
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
			            ->prepare("DELETE FROM acreedores WHERE id = ?");			          

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
			            ->prepare("DELETE FROM acreedores WHERE id_compra = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function SumarSaldo($data)
	{
		try 
		{
			$sql = "UPDATE acreedores SET saldo = saldo + ? WHERE id = ?";

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
			$sql = "UPDATE acreedores SET 
						id_cliente    = ?,
						id_compra      = ?,
						fecha      	  = ?,
						concepto      = ?, 
						monto         = ?,
						saldo         = ?,
						sucursal      = ?,
						moneda        = ?,
						cambio        = ?
                        
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$data->id_cliente,
                        $data->id_compra,
                        $data->fecha,
                        $data->concepto,                        
                        $data->monto,
                        $data->saldo,
                        $data->sucursal,
                        $data->moneda,
                        $data->cambio,
                        $data->id
					)
				);
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
				$anho = "AND YEAR(a.fecha) = $anho";
				$rango = "";
			 }else{
				 $anho = "";
			
					if($desde != ''){
						if($hasta!=''){
							$rango = " AND CAST(a.fecha as date) >= '$desde' AND CAST(a.fecha as date) <= '$hasta'";
						}else{
							$rango = " AND CAST(a.fecha as date) >= '$desde' AND CAST(a.fecha as date) <= '$fecha'";  
						}
					}else{
						if($hasta!=''){
							$rango = " AND CAST(a.fecha as date) >= '$mes' AND CAST(a.fecha as date) <= '$hasta'";
					}else{
						$rango = " AND CAST(a.fecha as date) >= '$mes' AND CAST(a.fecha as date) <= '$fecha'";  
					}
					} 
			
            }

			$sql = "SELECT a.concepto, a.fecha, SUM(a.monto) as monto, SUM(a.saldo) as saldo, c.nombre AS nombre
			FROM acreedores a
			LEFT JOIN clientes c ON a.id_cliente=c.id
			WHERE saldo > 0 $rango $anho 
			GROUP BY a.id_cliente 
			ORDER BY a.id DESC";
			          
			$stm = $this->pdo->prepare($sql);
			$stm->execute();
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
			$sql = "UPDATE acreedores SET 
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
	
	public function Restar($data)
	{
		try 
		{
			

			$sql = "UPDATE acreedores SET 
					
					saldo = saldo - ?
                        
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array( 
				        $data->monto,
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
		$sql = "INSERT INTO acreedores (id_cliente, id_compra, fecha, concepto, monto, saldo, sucursal, moneda, cambio) 
		        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->id_cliente,
					$data->id_compra,
					$data->fecha,
					$data->concepto,
                    $data->monto,
                    $data->saldo,
                    $data->sucursal,
                    $data->moneda,
                    $data->cambio
                    
                )
			);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}