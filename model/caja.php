<?php
class caja
{
	private $pdo;
    
    public $id;
    public $id_usuario;
    public $fecha;
    public $caja;
    public $monto;
    public $comprobante;
    public $anulado;

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
			session_start();
            if($_SESSION["nivel"]==1){
                $stm = $this->pdo->prepare("
				SELECT *, 
					(SELECT user FROM usuario u WHERE u.id = c.id_usuario) as usuario, 
					(SELECT SUM(i.monto) FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL GROUP BY i.id_caja) as ingresos,
					(SELECT SUM(e.monto) FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL GROUP BY e.id_caja) as egresos  
				FROM cajas c 
				ORDER BY c.id DESC");
				$stm->execute(array($_SESSION["user_id"]));
            }else{
                $stm = $this->pdo->prepare("
				SELECT *, 
					(SELECT user FROM usuario u WHERE u.id = c.id_usuario) as usuario, 
					(SELECT SUM(i.monto) FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL GROUP BY i.id_caja) as ingresos,
					(SELECT SUM(e.monto) FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL GROUP BY e.id_caja) as egresos  
				FROM cajas c 
				WHERE id_usuario = ? ORDER BY c.id DESC");
				$stm->execute(array($_SESSION["user_id"]));
            }
			

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
			
            $stm = $this->pdo->prepare("
			SELECT *, 
				(SELECT user FROM usuario u WHERE u.id = c.id_usuario) as usuario, 
				(SELECT SUM(i.monto) FROM ingresos i WHERE i.id_caja = c.id AND i.anulado IS NULL GROUP BY i.id_caja) as ingresos,
				(SELECT SUM(e.monto) FROM egresos e WHERE e.id_caja = c.id AND e.anulado IS NULL GROUP BY e.id_caja) as egresos  
			FROM cajas c ORDER BY c.id DESC");
			$stm->execute();


			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarUsuario($id_usuario)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, (SELECT user FROM usuario u WHERE u.id = c.id_usuario) as usuario FROM cajas c WHERE id_usuario = ? AND anulado <> 1 ORDER BY c.id DESC");
			$stm->execute(array($id_usuario));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarMovimientosCaja($id_caja)
	{
		try
		{
			$result = array();
			$query = "
				SELECT i.fecha, i.categoria, i.concepto, i.comprobante, (i.monto * 1) as monto, i.forma_pago, i.anulado, (SELECT v.descuento FROM ventas v WHERE v.id_venta = i.id_venta LIMIT 1) as descuento FROM ingresos i WHERE id_caja = ? AND anulado IS NULL
				UNION ALL 
				SELECT e.fecha, e.categoria, e.concepto, e.comprobante, (e.monto * -1) as monto, e.forma_pago, e.anulado, (SELECT v.descuento FROM compras v WHERE v.id_compra = e.id_compra LIMIT 1) as descuento FROM egresos e WHERE e.id_caja = ? AND anulado IS NULL ORDER BY fecha";
			$stm = $this->pdo->prepare($query);
			$stm->execute(array($id_caja ,$id_caja));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarAgrupado()
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM cajas GROUP BY caja ORDER BY id DESC");
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
			          ->prepare("SELECT * FROM cajas WHERE id = ?");
			          

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
			            ->prepare("DELETE FROM cajas WHERE id = ?");			          

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
			            ->prepare("UPDATE cajas SET anulado = 1 WHERE id = ?");			          

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
			$sql = "UPDATE cajas SET 
						id_usuario	  = ?,
						fecha	      = ?,
						caja          = ?, 
						monto         = ?,
						comprobante   = ?,
						anulado       = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$data->id_usuario,
				    	$data->fecha, 
                        $data->caja,                        
                        $data->monto,                       
                        $data->comprobante,
                        $data->anulado,
                        $data->id
					)
				);
		return "Modificado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar(caja $data)
	{
		try 
		{
		$sql = "INSERT INTO cajas (id_usuario, fecha, caja, monto, comprobante, anulado) 
		        VALUES (?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->id_usuario, 
                    $data->fecha,
                    $data->caja,
                    $data->monto,
                    $data->comprobante,
                    $data->anulado
                )
			);
		return "Agregado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}