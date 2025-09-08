<?php
class gift_card
{
	private $pdo;
    
    public $id;
    public $id_funcioniario;
    public $id_cliente;
    public $nro_tarjeta;
    public $monto;
    public $anulado;
    public $retirado;
    public $fecha;
    public $forma_pago;

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

			$stm = $this->pdo->prepare("SELECT *, g.id AS id, g.anulado AS anulado, u.user AS funcionario, c.nombre AS cliente FROM gift_card g
				LEFT JOIN usuario u ON u.id= g.id_funcionario
				LEFT JOIN clientes c ON c.id= g.id_cliente 
				ORDER BY g.id DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

public function ListarClientesSinAnular()
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, g.id AS id, g.anulado AS anulado  
				FROM gift_card g 
				LEFT JOIN clientes c ON c.id = g.id_cliente 
				WHERE g.anulado IS NULL AND g.retirado IS NULL  ORDER BY g.id DESC");
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
			          ->prepare("SELECT * FROM gift_card WHERE id = ?");
			          

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
			            ->prepare("DELETE FROM gift_card WHERE id = ?");			          

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
			            ->prepare("UPDATE  gift_card SET anulado = 1 WHERE id = ?");			          

			$stm->execute(array($id));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	public function Retirado($id)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE  gift_card SET retirado = 'RETIRADO' WHERE id = ?");			          

			$stm->execute(array($id));
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
			          ->prepare("SELECT MAX(id) as id FROM gift_card");
			$stm->execute();
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	
		public function BuscarTarjeta($nro_tarjeta)
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM gift_card WHERE nro_tarjeta = ? ");
			          

			$stm->execute(array($nro_tarjeta));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}


	public function Actualizar($data)
	{
		try 
		{
			$sql = "UPDATE gift_card SET 

						id_funcionario     = ?,
						id_cliente         = ?,
						nro_tarjeta        = ?,
						monto              = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$data->id_funcionario,
				    	$data->id_cliente,
				    	$data->nro_tarjeta,
				    	$data->monto,
				    	$data->id
					)
				);
		return "Modificado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar(gift_card $data)
	{
		try 
		{
		$sql = "INSERT INTO gift_card (id_funcionario, id_cliente, nro_tarjeta, monto, fecha, forma_pago, comprobante) 
		        VALUES (?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->id_funcionario,
					$data->id_cliente,
					$data->nro_tarjeta,
					$data->monto,
					$data->fecha,
					$data->forma_pago,
					$data->comprobante

					
					
                )
			);
		return "Agregado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}