<?php
class sucursal
{
	private $pdo;
    
    public $id;
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

			$stm = $this->pdo->prepare("SELECT * FROM sucursales ORDER BY sucursal DESC");
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
			          ->prepare("SELECT * FROM sucursales WHERE id = ?");
			          

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
			            ->prepare("DELETE FROM sucursales WHERE id = ?");			          

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
			$sql = "UPDATE sucursales SET 

						sucursal      		= ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$data->sucursal,
				    	$data->id
					)
				);
		return "Modificado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar(sucursal $data)
	{
		try 
		{
		$sql = "INSERT INTO sucursales (sucursal) 
		        VALUES (?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->sucursal
                )
			);
		return "Agregado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}