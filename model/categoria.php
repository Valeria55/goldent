<?php
class categoria
{
	private $pdo;
    
    public $id;
    public $id_padre;
    public $categoria;

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

			$stm = $this->pdo->prepare("SELECT * FROM categorias ORDER BY id_padre, categoria DESC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarPrincipal()
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM categorias WHERE id_padre = id ORDER BY categoria ASC");
			$stm->execute();

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}


	public function ListarSub($id_padre)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM categorias WHERE id_padre = ? AND id_padre <> id ORDER BY categoria ASC");
			$stm->execute(array($id_padre));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function Ultimo()
	{
		try 
		{
		    $result = array();
			$stm = $this->pdo
			          ->prepare("SELECT MAX(id) AS id FROM categorias ");
			          

			$stm->execute();
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
			          ->prepare("SELECT * FROM categorias WHERE id = ?");
			          

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
			            ->prepare("DELETE FROM categorias WHERE id = ?");			          

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
			$sql = "UPDATE categorias SET 

						id_padre        = ?,
						categoria      		= ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$data->id_padre,
				    	$data->categoria,
				    	$data->id
					)
				);
		return "Modificado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	public function GuardarPadre($id)
	{
		try 
		{
			$sql = "UPDATE categorias SET id_padre  = ? WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(array($id, $id));
		return "Modificado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar(categoria $data)
	{
		try 
		{
		$sql = "INSERT INTO categorias (id_padre, categoria) 
		        VALUES (?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->id_padre, 
					$data->categoria
                )
			);
		return "Agregado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}