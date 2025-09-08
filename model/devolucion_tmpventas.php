<?php
class devolucion_tmpventas
{
	private $pdo;
    
    public $id;
    public $id_venta;
    public $id_vendedor;
    public $id_producto;
    public $precio_venta;
    public $cantidad;
    public $descuento;
    public $fecha_venta;  
    
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
			if(!isset($_SESSION['user_id'])){
				session_start();
			}
			$userId= $_SESSION['user_id'];
			$result = array();

			$stm = $this->pdo->prepare("SELECT v.id, v.id_producto, v.id_vendedor, v.descuento, c.precio_costo, v.precio_venta, c.producto, c.precio_costo, v.cantidad, v.id_venta
				FROM devoluciones_tmpventas v 
				LEFT JOIN productos c ON v.id_producto = c.id 
				WHERE id_vendedor = ? 
				ORDER BY v.id DESC");
			$stm->execute(array($userId));

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
			          ->prepare("SELECT * FROM devoluciones_tmpventas WHERE id = ?");
			          

			$stm->execute(array($id));
			return $stm->fetch(PDO::FETCH_OBJ);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function ObtenerMoneda()
	{
		try 
		{
			$stm = $this->pdo
			          ->prepare("SELECT * FROM monedas WHERE id = 1");
			          

			$stm->execute();
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
			            ->prepare("DELETE FROM devoluciones_tmpventas WHERE id = ?");			          

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
			$stm = $this->pdo
			            ->prepare("DELETE FROM devoluciones_tmpventas ");
			$stm->execute();			          

		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Actualizar($data)
	{
		try 
		{
			$sql = "UPDATE devoluciones_tmpventas SET
						id_venta     = ?,
						id_vendedor     = ?,
						id_producto     = ?,
						precio_venta   = ?,
                        cantidad      = ?, 
						margen_ganancia     = ?,
						fecha_venta      = ?
						
				    WHERE id = ?";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
                        $data->id_venta,
                        $data->id_vendedor, 
                        $data->id_producto,                 
                        $data->precio_venta,
                        $data->cantidad,
                        $data->margen_ganancia, 
                        $data->fecha_venta,
                        $data->id
					)
				);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}

	public function Moneda($data)
	{
		try 
		{
			$sql = "UPDATE monedas SET
						reales     = ?,
						dolares     = ?,
						monto_inicial = ?
						";

			$this->pdo->prepare($sql)
			     ->execute(
				    array(
                        $data->reales,
                        $data->dolares,
                        $data->monto_inicial
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
		$sql = "INSERT INTO devoluciones_tmpventas (id_venta, id_vendedor, id_producto, precio_venta, cantidad, descuento, fecha_venta) 
		        VALUES (?, ?, ?, ?, ?, ?, ?)";

		$this->pdo->prepare($sql)
		     ->execute(
				array(
                    $data->id_venta,
                    $data->id_vendedor,
                    $data->id_producto,                 
                    $data->precio_venta,
                    $data->cantidad,
                    $data->descuento, 
                    $data->fecha_venta 
                   
                )
			);
		} catch (Exception $e) 
		{
			die($e->getMessage());
		}
	}
}