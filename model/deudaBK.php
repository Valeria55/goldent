<?php
class deuda
{
	private $pdo;
    
    public $id;
    public $id_cliente;
    public $id_venta;
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
						concepto      = ?, 
						monto         = ?,
						saldo         = ?,
						sucursal      = ?
                        
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$data->id_cliente,
                        $data->id_venta,
                        $data->fecha,
                        $data->concepto,                        
                        $data->monto,
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
	
	public function Restar($data)
	{
		try 
		{
			$sql = "UPDATE deudas SET 
					
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
	
	public function Guardar($data)
	{
		try 
		{
		$sql = "INSERT INTO deudas (id_cliente, id_venta, fecha, concepto, monto, saldo, sucursal) 
		        VALUES (?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->id_cliente,
					$data->id_venta,
					$data->fecha,
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
		$sql = "INSERT INTO deudas (id_cliente, id_venta, fecha, concepto, monto, saldo, sucursal) 
		        VALUES (?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->id_cliente,
					$data->id_venta,
					$data->fecha,
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
}