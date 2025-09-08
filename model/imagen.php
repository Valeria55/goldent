<?php
class imagen
{
	private $pdo;
    
    public $id;
    public $id_producto;
    public $imagen;

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

	public function Listar($id_producto)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM imagenes WHERE id_producto = ? ORDER BY id DESC");
			$stm->execute(array($id_producto));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function UnaImagen($id_producto)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM imagenes WHERE id_producto = ? ORDER BY id DESC LIMIT 1");
			$stm->execute(array($id_producto));

			return $stm->fetch(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function Dos($id_producto)
	{
		try
		{
			$result = array();

			$stm = $this->pdo->prepare("SELECT * FROM imagenes WHERE id_producto = ? ORDER BY id DESC LIMIT 2");
			$stm->execute(array($id_producto));

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
			          ->prepare("SELECT * FROM imagenes WHERE id = ?");
			          

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
			            ->prepare("DELETE FROM imagenes WHERE id = ?");			          

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
			$sql = "UPDATE imagenes SET 

						id_producto        = ?,
						imagen      		= ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$data->id_producto,
				    	$data->imagen,
				    	$data->id
					)
				);
		return "Modificado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar(imagen $data)
	{
		try 
		{
		$sql = "INSERT INTO imagenes (id_producto, imagen) 
		        VALUES (?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					$data->id_producto, 
					$data->imagen
                )
			);
		return "Agregado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}