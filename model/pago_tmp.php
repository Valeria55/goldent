<?php
class pago_tmp
{
	private $pdo;
    
    public $id;
    public $pago;
    public $monto;
	public $moneda;

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
		    //session_start();
			$id_usuario = $_SESSION["user_id"];

			$stm = $this->pdo->prepare("SELECT * FROM pagos_tmp WHERE id_usuario = ? ORDER BY id DESC");
			$stm->execute(array($id_usuario));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}
	

	public function Obtener()
	{
		try 
		{
		    //session_start();
			$id_usuario = $_SESSION["user_id"];
			
			$stm = $this->pdo
			          ->prepare("SELECT SUM(monto) as monto FROM pagos_tmp WHERE id_usuario = ?");
			$stm->execute(array($id_usuario));
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
			            ->prepare("DELETE FROM pagos_tmp WHERE id = ?");			          

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
			$id_usuario = $_SESSION["user_id"];
			$stm = $this->pdo
			            ->prepare("DELETE FROM pagos_tmp WHERE id_usuario = ?");			          

			$stm->execute(array($id_usuario));
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try 
		{
			$sql = "UPDATE pago_tmpes SET 

						id_producto        = ?,
						pago_tmp      		= ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$data->id_producto,
				    	$data->pago_tmp,
				    	$data->id
					)
				);
		return "Modificado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar(pago_tmp $data)
	{
		try 
		{
		$sql = "INSERT INTO pagos_tmp (id_usuario, pago, monto, moneda, tarjeta) 
		        VALUES (?, ?, ?, ?, ?)";


		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->id_usuario,
					$data->pago, 
					$data->monto,
					$data->moneda,
					$data->tarjeta
                )
			);
		return "Agregado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}