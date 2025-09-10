<?php
class inventario
{
	private $pdo;
    
    public $id;
    public $id_producto;
    public $id_usuario;
    public $stock_actual;
    public $stock_real;
    public $faltante;
    public $fecha;

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
//Lista la fecha del dia
	public function Listar($fecha)
	{
		try
		{
			
			$result = array();

			$stm = $this->pdo->prepare("SELECT *, i.id AS id_i, p.codigo AS codigo, p.producto AS producto, u.user AS usuario 
				FROM inventario i
				LEFT JOIN productos p ON i.id_producto=p.id
				LEFT JOIN usuario u ON i.id_usuario = u.id
				WHERE i.fecha = ?
				ORDER BY id_i DESC");
			//le da como parametro fecha
			$stm->execute(array($fecha));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}

	public function ListarRango($desde)
	{
		try
		{
			

			$result = array();

			$stm = $this->pdo->prepare("SELECT *, i.id AS id_i, p.codigo AS codigo, p.producto AS producto, u.user AS usuario 
				FROM inventario i
				LEFT JOIN productos p ON i.id_producto=p.id
				LEFT JOIN usuario u ON i.id_usuario = u.id
				WHERE i.fecha = ?
				ORDER BY id_i DESC");
			//le da como parametro fecha
			$stm->execute(array($desde));

			return $stm->fetchAll(PDO::FETCH_OBJ);
		}
		catch(Exception $e)
		{
			die($e->getMessage());
		}
	}


	public function StockReal($data)
	{
		try 
		{
			$sql = "UPDATE inventario  SET stock_real = ?, faltante = ?  WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
                        $data->stock_real,
                        $data->faltante,
                        $data->id
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
			          ->prepare("SELECT * FROM inventario WHERE id = ?");
			          

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
			            ->prepare("DELETE FROM inventario WHERE id = ?");			          

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
			$sql = "UPDATE inventario SET 

						inventario      		= ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
				    	$data->id_producto,
				    	$data->id_usuario,
				    	$data->stock_actual,
				    	$data->stock_real,
				    	$data->faltante,
				    	$data->id
					)
				);
		return "Modificado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Registrar(inventario $data)
	{
		try 
		{
		$sql = "INSERT INTO inventario (id_producto, id_usuario, stock_actual, stock_real, faltante, fecha) 
		        VALUES (?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
						$data->id_producto,
				    	$data->id_usuario,
				    	$data->stock_actual,
				    	$data->stock_real,
				    	$data->faltante,
				    	$data->fecha
                )
			);
		return "Agregado";
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}