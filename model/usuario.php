<?php
class usuario
{
	private $pdo;
    
    public $id;
    public $user;
    public $pass;
    public $nivel;
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

			$stm = $this->pdo->prepare("SELECT *, u.id, s.sucursal FROM usuario u LEFT JOIN sucursales s ON u.sucursal = s.id ORDER BY u.id DESC");
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
			          ->prepare("SELECT * FROM usuario WHERE id = ?");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
	
	public function ChangePass($data)
	{
		try 
		{
			$stm = $this->pdo
			            ->prepare("UPDATE usuario SET pass = ? WHERE id = ?");			          

			$stm->execute(array($data->pass, $data->id));
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
			            ->prepare("DELETE FROM usuario WHERE id = ?");			          

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
			$sql = "UPDATE usuario SET 
						user      		= ?,
						pass          = ?, 
						nivel        = ?,
						sucursal        = ?,
						comision       = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$data->user, 
                        $data->pass,                        
                        $data->nivel,                       
                        $data->sucursal,                       
                        $data->comision, 
                        $data->id
					)
				);
		return "Modificado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar(usuario $data)
	{
		try 
		{
		$sql = "INSERT INTO usuario (user, pass, nivel, sucursal, comision) 
		        VALUES (?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
					 $data->user, 
                    $data->pass,
                    $data->nivel,
                    $data->sucursal,
                    $data->comision
                )
			);
		return "Agregado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}