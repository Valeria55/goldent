<?php
class transferencia
{
	private $pdo;
    
    public $id;
    public $usuario_emisor;
    public $usuario_receptor;
    public $local_emisor;
    public $local_receptor;
    public $id_producto;
    public $cantidad;
    public $tipo;
    public $fecha_solicitada;
    public $fecha_aceptada;
    public $observacion;  
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
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT *,
			 (SELECT user FROM usuario u WHERE u.id = t.usuario_emisor) as emisor,
			 (SELECT producto FROM productos p WHERE p.id = t.id_producto) as producto,
			 (SELECT user FROM usuario u WHERE u.id = t.usuario_receptor) as receptor,
			 (SELECT sucursal FROM sucursales s WHERE s.id = t.local_emisor) as suc_emisor,
			 (SELECT sucursal FROM sucursales s WHERE s.id = t.local_receptor) as suc_receptor 
			 FROM transferencias t ORDER BY id DESC");
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
			$stm = $this->pdo->prepare("SELECT *, e.id as id FROM egresos e LEFT JOIN clientes c ON e.id_cliente = c.id WHERE e.id_acreedor = ? ORDER BY e.id DESC");
			$stm->execute(array($id_acreedor));

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
			          ->prepare("SELECT * FROM egresos WHERE MONTH(fecha) = MONTH(?) AND YEAR(fecha) = YEAR(?) AND categoria <> 'compra' ");
			          

			$stm->execute(array($fecha, $fecha));
			return $stm->fetchAll(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
		public function Listar_rango($desde,$hasta)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, e.id as id FROM egresos e LEFT JOIN clientes c ON e.id_cliente = c.id WHERE cast(fecha as date) >= ? AND cast(fecha as date) <= ? ORDER BY e.id DESC");
			$stm->execute(array($desde,$hasta));

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

			$stm = $this->pdo->prepare("SELECT * FROM egresos WHERE categoria <> 'compra' AND Cast(fecha as date) = ? ORDER BY id DESC");
			$stm->execute(array($fecha));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	
	public function ListarSincompraMes($fecha)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM egresos WHERE categoria <> 'compra' AND MONTH(fecha) = MONTH(?) AND YEAR(fecha) = YEAR(?) ORDER BY id DESC");
			$stm->execute(array($fecha,$fecha));

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


	public function Obtener($id)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM transferencias WHERE id = ?");
			          

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
			            ->prepare("DELETE FROM egresos WHERE id = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Aceptar($id, $usuario_receptor, $fecha_aceptada)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE transferencias SET estado = 'Aceptado', usuario_receptor = ?, fecha_aceptada = ? WHERE id = ?");			          

			$stm->execute(array($usuario_receptor,$fecha_aceptada, $id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function FinalizarCarga($id)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE transferencias SET observacion = '' WHERE usuario_emisor = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Cancelar($id, $estado)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE transferencias SET estado = ? WHERE id = ?");			          

			$stm->execute(array($estado,$id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	public function Borrar($id)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("DELETE FROM transferencias WHERE id = ?");			          

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
			$sql = "UPDATE transferencias SET 
			            usuario_emisor     = ?,
			            usuario_receptor   = ?,
			            local_emisor       = ?,
						local_receptor     = ?,
						id_producto        = ?,
						cantidad           = ?, 
						tipo               = ?, 
						fecha_solicitada   = ?,
						fecha_aceptada     = ?,
						observacion        = ?,
						estado             = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(					    
						$data->usuario_emisor,
						$data->usuario_receptor,
						$data->local_emisor,
	                	$data->local_receptor, 
	                	$data->id_producto,
	                	$data->cantidad, 
	                	$data->tipo,                        
	                	$data->fecha_solicitada,
	                	$data->fecha_aceptada,
	                	$data->observacion,
	                	$data->estado,
                   		$data->id
                	)
				);
				echo $sql;
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar($data)
	{
		try 
		{
		$sql = "INSERT INTO transferencias(id, usuario_emisor, usuario_receptor, local_emisor, local_receptor, id_producto, cantidad, tipo, fecha_solicitada, fecha_aceptada, observacion, estado) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(

				    $data->id,
					$data->usuario_emisor,
					$data->usuario_receptor,
					$data->local_emisor,
                	$data->local_receptor, 
                	$data->id_producto,
                	$data->cantidad, 
                	$data->tipo,                        
                	$data->fecha_solicitada,
                	$data->fecha_aceptada,
                	$data->observacion,
                	$data->estado
                   
                )
			);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}